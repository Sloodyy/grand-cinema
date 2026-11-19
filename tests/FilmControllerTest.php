<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FilmControllerTest extends WebTestCase
{
    public function testFilmsPageIsAccessible(): void
    {
        $client = static::createClient();
        $client->request('GET', '/films');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Notre programmation');
        $this->assertSelectorTextContains('h2', 'Les films');
    }
}
