<?php

namespace TournamentBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ParticipationControllerTest extends WebTestCase
{
    public function testParticipatein()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/participate');
    }

    public function testMyparticipations()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/myParticipations');
    }

}
