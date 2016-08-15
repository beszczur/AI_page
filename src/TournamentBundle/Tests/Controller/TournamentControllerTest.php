<?php

namespace TournamentBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TournamentControllerTest extends WebTestCase
{
    public function testShowtournament()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/show');
    }

    public function testEdittournament()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/edit');
    }

    public function testListtournament()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', 'list');
    }

}
