<?php

namespace App\Tests;


use App\Form\UserType;
use App\Repository\UserRepository;
use Generator;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

class UserTest extends WebTestCase
{

    /**
     * @dataProvider provideEmails
     */
    public function testEditUser(): void
    {
        $client = static::createClient();

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get("router");

        $userRepo = $client->getContainer()->get(UserRepository::class);
        $user = $userRepo->findOneBy([
            'email' => 'admin@medicalware.fr'
        ]);

        $client = $client->loginUser($user);

        $crawler = $client->request(Request::METHOD_GET, $router->generate("app_edit_user", ['id' => $user->getId()]));

        $formId = UserType::FORM_ID;
        $selector = sprintf("#%s", $formId);

        $form = $crawler->filter($selector)->form([
            'user[lastName]' => 'qsd',
            'user[firstName]' => 'qsdqsd',
            'user[phoneNumber]' => '0712124578',
            'user[birthdayDate]' => '2022-04-19',
            'user[email]' => 'admin@medicalware.fr',
            'user[isActive]' => '1',
            'user[profession]' => '3',
            'user[address][street]' => '50 rue de la rue',
            'user[address][complementaryInfo]' => '',
            'user[address][postalCode]' => '13015',
            'user[address][city]' => 'Marseille',
        ]);

        $client->submit($form);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    /**
     * @return Generator
     */
    public function provideEmails(): Generator
    {
        yield ['admin@medicalware.fr'];
    }
}
