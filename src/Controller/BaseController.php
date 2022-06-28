<?php

namespace App\Controller;

use App\Entity\EntityInterface;
use App\Entity\User;
use App\Service\DataFormatterInterface;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;

class BaseController extends AbstractController
{
    use ResetPasswordControllerTrait;


    public const LIMIT = 2;

    /**
     * @return array
     */
    public static function getSubscribedServices(): array
    {
        return array_merge(
            parent::getSubscribedServices(),
            [
                EntityManagerInterface::class => EntityManagerInterface::class,
                RequestStack::class => RequestStack::class,
            ],
        );
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function paginateRequest(string $entity, Request $request, DataFormatterInterface $formatter, \Closure $modifier = null): JsonResponse
    {
        $page = $request->query->get('page', 1);
        $query = $request->query->get('query');
        $limit = $request->query->get('limit', self::LIMIT);
        $filters = $request->query->get('filters');
        $filters = json_decode($filters, true) ?? [];

        $manager = $this->container->get(EntityManagerInterface::class);
        $datas = $manager->getRepository($entity)->paginate($page, $limit, $query, $modifier, $filters);

        $data = [];

        foreach ($datas as $user) {
            $data[] = $formatter->format($user);
        }

        return $this->json([
            'data' => $data,
            'filteredCount' => $datas->count(),
            'totalCount' => $manager->getRepository($entity)->count([])
        ]);
    }


    public function redirectToReferer(): RedirectResponse|Response
    {
        try{
            $requestStack = $this->container->get(RequestStack::class);
        } catch (NotFoundExceptionInterface|ContainerExceptionInterface) {
            return new Response("Une erreur est survenue");
        }

        $request = $requestStack->getCurrentRequest();

        $referer = array_values($request->request->all())[0]['referer'];

        return $this->redirect($referer);
    }


}
