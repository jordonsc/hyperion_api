<?php

namespace Hyperion\ApiBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/api/v1/projects.yml');

        $this->assertContains('projects', $crawler->html());
    }
}
