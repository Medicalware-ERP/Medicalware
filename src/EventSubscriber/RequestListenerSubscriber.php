<?php

namespace App\EventSubscriber;

use App\Entity\User;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Security;

class RequestListenerSubscriber implements EventSubscriberInterface
{
    public function __construct(private Security $security, private UrlGeneratorInterface $generator)
    {
    }

    public function onKernelRequest(RequestEvent $event)
    {
        /** @var User $user */
        $user = $this->security->getUser();

        if ($user instanceof User && !$user->isActive()) {
            $event->setResponse(new RedirectResponse($this->generator->generate('app_logout')));
        }
    }

    #[ArrayShape(['kernel.request' => "string"])]
    public static function getSubscribedEvents(): array
    {
        return [
            'kernel.request' => 'onKernelRequest',
        ];
    }
}
