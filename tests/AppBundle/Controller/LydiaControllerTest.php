<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Service\Lydia;
use AppBundle\Service\Lydia\Status;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LydiaControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $status = $this->createMock(Status::class);
        $status->method('getStateMessage')
            ->willReturn('1234');

        $lydia = $this->createMock(Lydia::class);
        $lydia->method('status')
            ->willReturn($status);

        $client->getContainer()->set('lydia', $lydia);

        $crawler = $client->request('GET', '/status/1');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('1234', $crawler->text());
    }
}
