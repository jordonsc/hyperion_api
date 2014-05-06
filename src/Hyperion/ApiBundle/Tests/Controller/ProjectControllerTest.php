<?php

namespace Hyperion\ApiBundle\Tests\Controller;

use FOS\RestBundle\Util\Codes;
use Guzzle\Http\Client;
use Guzzle\Http\Exception\BadResponseException;
use Hyperion\ApiBundle\Entity\Project;
use Hyperion\ApiBundle\Collection\ProjectCollection;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProjectControllerTest extends WebTestCase
{

    const BASE_URL = 'http://api.hyperion.dev';

    /**
     * @small
     */
    public function testProjectFailedValidation()
    {
        $http_client = new Client(self::BASE_URL);
        $post_data   = ['name' => 'a'];

        try {
            $http_client->post('/api/v1/project', [], $post_data)->send();
            $this->fail("Request succeeded when it shouldn't have");
        } catch (BadResponseException $e) {
            $this->assertEquals(400, $e->getResponse()->getStatusCode());
        }
    }

    /**
     * @small
     */
    public function testProjectGet404()
    {
        $http_client = new Client(self::BASE_URL);

        try {
            $http_client->get('/api/v1/project/1234567')->send();
            $this->fail("Request succeeded when it shouldn't have");
        } catch (\Guzzle\Http\Exception\ClientErrorResponseException $e) {
            $this->assertEquals(404, $e->getResponse()->getStatusCode());
        }
    }

    /**
     * @small
     */
    public function testProjectDelete404()
    {
        $http_client = new Client(self::BASE_URL);

        try {
            $http_client->delete('/api/v1/project/1234567')->send();
            $this->fail("Request succeeded when it shouldn't have");
        } catch (\Guzzle\Http\Exception\ClientErrorResponseException $e) {
            $this->assertEquals(404, $e->getResponse()->getStatusCode());
        }
    }

    /**
     * @medium
     */
    public function testProjectCrud()
    {
        $serializer  = static::createClient()->getContainer()->get('jms_serializer');
        $http_client = new Client(self::BASE_URL);
        $response    = null;
        $test_name   = 'Project #'.rand(100, 999);
        $post_data   = ['name' => $test_name];

        // CREATE
        try {
            $response = $http_client->post('/api/v1/project', [], $post_data)->send();
            $this->assertEquals(Codes::HTTP_CREATED, $response->getStatusCode());

        } catch (BadResponseException $e) {
            $this->fail('Server returned '.$e->getResponse()->getStatusCode().': '.$e->getResponse()->getBody());
        }

        /** @var $created \Hyperion\ApiBundle\Entity\Project */
        $created = $serializer->deserialize(
            $response->getBody(),
            'Hyperion\ApiBundle\Entity\Project',
            'json'
        );

        $this->assertGreaterThan(0, $created->getId());
        $this->assertEquals($test_name, $created->getName());
        $this->assertEquals(0, count($created->getActions()));
        $this->assertEquals(0, count($created->getDistributions()));


        // RETRIEVE
        $response = $http_client->get('/api/v1/project/'.$created->getId())->send();
        $this->assertEquals(Codes::HTTP_OK, $response->getStatusCode());

        /** @var $retrieved \Hyperion\ApiBundle\Entity\Project */
        $retrieved = $serializer->deserialize(
            $response->getBody(),
            'Hyperion\ApiBundle\Entity\Project',
            'json'
        );

        $this->assertEquals($created->getId(), $retrieved->getId());
        $this->assertEquals($test_name, $retrieved->getName());
        $this->assertEquals(0, count($retrieved->getActions()));
        $this->assertEquals(0, count($retrieved->getDistributions()));

        // RETRIEVE ALL
        $response = $http_client->get('/api/v1/projects')->send();
        $this->assertEquals(Codes::HTTP_OK, $response->getStatusCode());

        /** @var $retrieved ProjectCollection */
        $retrieved_all = new ProjectCollection($serializer->deserialize(
            $response->getBody(),
            'ArrayCollection<Hyperion\ApiBundle\Entity\Project>',
            'json'
        ));

        $this->assertGreaterThan(0, $retrieved_all->count());

        $item = $retrieved_all->getById($created->getId());
        $this->assertEquals($created->getId(), $item->getId());
        $this->assertEquals($test_name, $item->getName());
        $this->assertEquals(0, count($item->getActions()));
        $this->assertEquals(0, count($item->getDistributions()));

        $item = $retrieved_all->getByName($test_name);
        $this->assertEquals($created->getId(), $item->getId());
        $this->assertEquals($test_name, $item->getName());

        // UPDATE
        $update_data = [
            'name' => 'Updated name #'.rand(100, 999),
        ];

        try {
            $response = $http_client->put('/api/v1/project/'.$created->getId(), [], $update_data)->send();
            $this->assertEquals(Codes::HTTP_OK, $response->getStatusCode());

        } catch (BadResponseException $e) {
            $this->fail('Server returned '.$e->getResponse()->getStatusCode().': '.$e->getResponse()->getBody());
        }

        /** @var $created \Hyperion\ApiBundle\Entity\Project */
        $updated = $serializer->deserialize(
            $response->getBody(),
            'Hyperion\ApiBundle\Entity\Project',
            'json'
        );

        $this->assertGreaterThan(0, $updated->getId());
        $this->assertEquals($update_data['name'], $updated->getName());
        $this->assertEquals(0, count($updated->getActions()));
        $this->assertEquals(0, count($updated->getDistributions()));


        // DELETE
        /** @var $item Project */
        foreach ($retrieved_all as $item) {
            $response = $http_client->delete('/api/v1/project/'.$item->getId())->send();
            $this->assertEquals(Codes::HTTP_OK, $response->getStatusCode());
        }

        $response = $http_client->get('/api/v1/projects')->send();

        /** @var $retrieved ProjectCollection */
        $retrieved_all = new ProjectCollection($serializer->deserialize(
            $response->getBody(),
            'ArrayCollection<Hyperion\ApiBundle\Entity\Project>',
            'json'
        ));

        $this->assertEquals(0, $retrieved_all->count());

    }


}
