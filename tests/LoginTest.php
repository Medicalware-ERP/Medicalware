<?php

namespace App\Tests;


use Generator;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

class LoginTest extends WebTestCase
{
    private static function login(string $email, KernelBrowser $client = null) {
        $client = static::createClient();

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get("router");

        $crawler = $client->request(Request::METHOD_GET, $router->generate("app_login"));

        $form = $crawler->filter("form[name=login]")->form([
            "email" => $email,
            "password" => "admin"
        ]);

        $client->submit($form);
    }
    /**
     * @param string $email
     * @dataProvider provideEmails
     */
    public function testIfLoginIsSuccessful(string $email): void
    {
        $client = static::createClient();

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get("router");

        $crawler = $client->request(Request::METHOD_GET, $router->generate("app_login"));

        $form = $crawler->filter("form[name=login]")->form([
            "email" => $email,
            "password" => "admin"
        ]);

        $client->submit($form);

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();

        $this->assertRouteSame('dashboard');
    }

    /**
     * @return Generator
     */
    public function provideEmails(): Generator
    {
        yield ['admin@medicalware.fr'];
    }

    /**
     * @param string $email
     * @dataProvider provideEmails
     */
    public function testInvalidCsrfTokenLogin(string $email): void
    {
        $client = static::createClient();

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get("router");

        $crawler = $client->request(Request::METHOD_GET, $router->generate("app_login"));

        $form = $crawler->filter("form[name=login]")->form([
            "_csrf_token" => "fail",
            "email" => $email,
            "password" => "password"
        ]);

        $client->submit($form);

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();

        $this->assertSelectorTextContains("div.alert-danger", 'Invalid CSRF token.');
    }

    public function testInvalidCredentials(): void
    {
        $client = static::createClient();

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get("router");

        $crawler = $client->request(Request::METHOD_GET, $router->generate("app_login"));

        $form = $crawler->filter("form[name=login]")->form([
            "email" => "admin@medicalware.fr",
            "password" => "fail"
        ]);

        $client->submit($form);

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();

        $this->assertSelectorTextContains("div.alert-danger", 'Invalid credentials.');
    }

    public function testInvalidEmail(): void
    {
        $client = static::createClient();

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get("router");

        $crawler = $client->request(Request::METHOD_GET, $router->generate("app_login"));

        $form = $crawler->filter("form[name=login]")->form([
            "email" => "fail@email.com",
            "password" => "password"
        ]);

        $client->submit($form);

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();

        $this->assertSelectorTextContains("div.alert-danger", 'Invalid credentials.');
    }

}
