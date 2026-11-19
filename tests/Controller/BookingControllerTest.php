<?php

namespace App\Tests\Controller;

use App\Entity\Film;
use App\Entity\Session;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class BookingControllerTest extends WebTestCase
{
    public function testBookingPageIsAccessible(): void
    {
        $client = static::createClient();

        $entityManager = static::getContainer()
            ->get(EntityManagerInterface::class);

        $film = new Film();
        $film->setTitle('Film de test');
        $film->setDuration(120);

        $session = new Session();
        $session->setFilm($film);
        $session->setStartsAt(new \DateTimeImmutable('2026-11-20 14:00:00'));
        $session->setRoomNumber(1);
        $session->setPrice('9.50');

        $entityManager->persist($film);
        $entityManager->persist($session);
        $entityManager->flush();

        $client->request('GET', '/booking/' . $session->getId());

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'Réserver mes places');
    }
}
