<?php

namespace RegisterBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ErrorsControllerTest extends WebTestCase
{
    public function testLimitexceed()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/errors/limitExceed');
    }

}
