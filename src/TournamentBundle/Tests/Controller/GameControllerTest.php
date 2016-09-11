<?php

namespace TournamentBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class GameControllerTest extends WebTestCase
{
    public function testListgames()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/games');
    }

}
