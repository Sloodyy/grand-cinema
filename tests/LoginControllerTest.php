<?php

namespace App\Tests;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class LoginControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $container = static::getContainer();
        $em = $container->get('doctrine.orm.entity_manager');
        $userRepository = $em->getRepository(User::class);

        foreach ($userRepository->findAll() as $user) {
            $em->remove($user);
        }

        $em->flush();

        $passwordHasher = $container->get('security.user_password_hasher');

        $user = new User();
        $user->setEmail('email@example.com');
        $user->setFirstName('Jean');
        $user->setLastName('Dupont');
        $user->setPassword(
            $passwordHasher->hashPassword($user, 'password')
        );

        $em->persist($user);
        $em->flush();
    }

    public function testLogin(): void
    {
        $this->client->request('GET', '/login');
        self::assertResponseIsSuccessful();

        // Connexion avec un email inexistant
        $this->client->submitForm('Se connecter', [
            '_username' => 'doesNotExist@example.com',
            '_password' => 'password',
        ]);

        self::assertResponseRedirects('/login');
        $this->client->followRedirect();
        self::assertSelectorExists('.alert-danger');

        // Connexion avec un mauvais mot de passe
        $this->client->request('GET', '/login');

        $this->client->submitForm('Se connecter', [
            '_username' => 'email@example.com',
            '_password' => 'bad-password',
        ]);

        self::assertResponseRedirects('/login');
        $this->client->followRedirect();
        self::assertSelectorExists('.alert-danger');

        // Connexion avec les bons identifiants
        $this->client->request('GET', '/login');

        $this->client->submitForm('Se connecter', [
            '_username' => 'email@example.com',
            '_password' => 'password',
        ]);

        self::assertResponseRedirects('/');
        $this->client->followRedirect();

        self::assertSelectorNotExists('.alert-danger');
    }
}
