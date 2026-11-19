<?php

namespace App\Tests;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegistrationControllerTest extends WebTestCase
{
    public function testRegister(): void
{
    $client = static::createClient();

    $email = 'test-' . uniqid() . '@example.com';

    $client->request('GET', '/register');

    self::assertResponseIsSuccessful();
    self::assertPageTitleContains('Créer un compte');

    $client->submitForm('Créer mon compte', [
        'registration_form[email]' => $email,
        'registration_form[firstName]' => 'Jean',
        'registration_form[lastName]' => 'Dupont',
        'registration_form[plainPassword]' => 'password123',
        'registration_form[agreeTerms]' => true,
    ]);

    self::assertResponseRedirects();

    $userRepository = static::getContainer()->get(UserRepository::class);

    $user = $userRepository->findOneBy([
        'email' => $email,
    ]);

    self::assertNotNull($user);
    self::assertNotSame('password123', $user->getPassword());
}
}
