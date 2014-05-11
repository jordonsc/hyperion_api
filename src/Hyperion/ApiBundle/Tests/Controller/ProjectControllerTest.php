<?php

namespace Hyperion\ApiBundle\Tests\Controller;

use FOS\RestBundle\Util\Codes;
use Guzzle\Http\Client;
use Guzzle\Http\Exception\BadResponseException;
use Guzzle\Http\Exception\ServerErrorResponseException;
use Hyperion\ApiBundle\Entity\Project;
use Hyperion\ApiBundle\Collection\EntityCollection;
use Hyperion\Dbal\Collection\CriteriaCollection;
use Hyperion\Dbal\Enum\Comparison;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Does the same thing CrudControllerTest does except a little more specific and tests search functionality
 *
 * Only applies to the 'Project' entity
 */
class ProjectControllerTest extends WebTestCase
{

    const BASE_URL = 'http://api.hyperion.dev';

    protected function getSamplePayload()
    {
        return [
            'name'                   => 'Sample Project',
            'bake_status'            => 0,
            'baked_image_id'         => null,
            'source_image_id'        => 'i-fake',
            'packager'               => 0,
            'update_system_packages' => 0,
            'packages'               => '[]',
            'script'                 => null,
            'services'               => '[]',
        ];
    }

    /**
     * @small
     */
    public function testProjectFailedValidation()
    {
        $http_client       = new Client(self::BASE_URL);
        $post_data         = $this->getSamplePayload();
        $post_data['name'] = 'a';

        try {
            $http_client->post('/api/v1/project/new', [], $post_data)->send();
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
        $serializer        = static::createClient()->getContainer()->get('jms_serializer');
        $http_client       = new Client(self::BASE_URL);
        $response          = null;
        $test_name         = 'Project #'.rand(100, 999);
        $post_data         = $this->getSamplePayload();
        $post_data['name'] = $test_name;

        // CREATE
        try {
            $response = $http_client->post('/api/v1/project/new', [], $post_data)->send();
            $this->assertEquals(Codes::HTTP_CREATED, $response->getStatusCode());

        } catch (BadResponseException $e) {
            $this->fail('Server returned '.$e->getResponse()->getStatusCode().': '.$e->getResponse()->getBody());
        }

        /** @var $created Project */
        $created = $serializer->deserialize(
            $response->getBody(),
            'Hyperion\Dbal\Entity\Project',
            'json'
        );

        $this->assertGreaterThan(0, $created->getId());
        $this->assertEquals($test_name, $created->getName());


        // RETRIEVE
        $response = $http_client->get('/api/v1/project/'.$created->getId())->send();
        $this->assertEquals(Codes::HTTP_OK, $response->getStatusCode());

        /** @var $retrieved Project */
        $retrieved = $serializer->deserialize(
            $response->getBody(),
            'Hyperion\Dbal\Entity\Project',
            'json'
        );

        $this->assertEquals($created->getId(), $retrieved->getId());
        $this->assertEquals($test_name, $retrieved->getName());

        // RETRIEVE ALL
        $response = $http_client->get('/api/v1/project/all')->send();
        $this->assertEquals(Codes::HTTP_OK, $response->getStatusCode());

        $retrieved_all = new EntityCollection($serializer->deserialize(
            $response->getBody(),
            'ArrayCollection<Hyperion\Dbal\Entity\Project>',
            'json'
        ));

        $this->assertGreaterThan(0, $retrieved_all->count());

        $item = $retrieved_all->getById($created->getId());
        $this->assertEquals($created->getId(), $item->getId());
        $this->assertEquals($test_name, $item->getName());

        $item = $retrieved_all->getBy($test_name, 'name');
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

        /** @var $created Project */
        $updated = $serializer->deserialize(
            $response->getBody(),
            'Hyperion\Dbal\Entity\Project',
            'json'
        );

        $this->assertGreaterThan(0, $updated->getId());
        $this->assertEquals($update_data['name'], $updated->getName());


        // DELETE
        /** @var $item Project */
        foreach ($retrieved_all as $item) {
            $response = $http_client->delete('/api/v1/project/'.$item->getId())->send();
            $this->assertEquals(Codes::HTTP_OK, $response->getStatusCode());
        }

        $response = $http_client->get('/api/v1/project/all')->send();

        /** @var $retrieved EntityCollection */
        $retrieved_all = new EntityCollection($serializer->deserialize(
            $response->getBody(),
            'ArrayCollection<Hyperion\Dbal\Entity\Project>',
            'json'
        ));

        $this->assertEquals(0, $retrieved_all->count());

    }

    /**
     * @medium
     */
    public function testProjectSearch()
    {
        $this->cleanProjects();

        $post_data_a         = $this->getSamplePayload();
        $post_data_a['name'] = "Search Project Alpha";

        $post_data_b         = $this->getSamplePayload();
        $post_data_b['name'] = "Search Project Bravo";

        $http_client = new Client(self::BASE_URL);
        $http_client->post('/api/v1/project/new', [], $post_data_a)->send();
        $http_client->post('/api/v1/project/new', [], $post_data_b)->send();

        // LIKE
        $criteria = CriteriaCollection::build()->add('name', '%Alpha', Comparison::LIKE());
        $r        = $this->doSearch($criteria);
        $this->assertEquals(1, $r->count());
        $this->assertEquals("Search Project Alpha", $r->current()->getName());

        // LIKE
        $criteria = CriteriaCollection::build()->add('name', '%Project%', Comparison::LIKE());
        $r        = $this->doSearch($criteria);
        $this->assertEquals(2, $r->count());

        // LIKE
        $criteria = CriteriaCollection::build()->add('name', 'fake', Comparison::LIKE());
        $r        = $this->doSearch($criteria);
        $this->assertEquals(0, $r->count());

        // NOT LIKE
        $criteria = CriteriaCollection::build()->add('name', '%Bravo%', Comparison::NOT_LIKE());
        $r        = $this->doSearch($criteria);
        $this->assertEquals(1, $r->count());
        $this->assertEquals("Search Project Alpha", $r->current()->getName());

        // EQUALS
        $criteria = CriteriaCollection::build()->add('name', 'Search Project Bravo', Comparison::EQUALS());
        $r        = $this->doSearch($criteria);
        $this->assertEquals(1, $r->count());
        $this->assertEquals("Search Project Bravo", $r->current()->getName());

        // NOT EQUALS
        $criteria = CriteriaCollection::build()->add('name', 'Search Project Bravo', Comparison::NOT_EQUALS());
        $r        = $this->doSearch($criteria);
        $this->assertEquals(1, $r->count());
        $this->assertEquals("Search Project Alpha", $r->current()->getName());

        // GREATER THAN EQUALS
        $criteria = CriteriaCollection::build()->add('id', 1, Comparison::GTE());
        $r        = $this->doSearch($criteria);
        $this->assertEquals(2, $r->count());

        $this->cleanProjects();
    }

    protected function cleanProjects()
    {
        $http_client = new Client(self::BASE_URL);
        $serializer  = static::createClient()->getContainer()->get('jms_serializer');

        $response = $http_client->get('/api/v1/project/all')->send();
        $this->assertEquals(Codes::HTTP_OK, $response->getStatusCode());

        /** @var $retrieved EntityCollection */
        $retrieved_all = new EntityCollection($serializer->deserialize(
            $response->getBody(),
            'ArrayCollection<Hyperion\Dbal\Entity\Project>',
            'json'
        ));

        /** @var $item Project */
        foreach ($retrieved_all as $item) {
            $response = $http_client->delete('/api/v1/project/'.$item->getId())->send();
            $this->assertEquals(Codes::HTTP_OK, $response->getStatusCode());
        }
    }

    protected function doSearch(CriteriaCollection $criteria, $response_code = 200)
    {
        $serializer  = static::createClient()->getContainer()->get('jms_serializer');
        $http_client = new Client(self::BASE_URL);
        $response    = null;
        $payload     = $serializer->serialize($criteria->getItems(), 'json');

        try {
            $response = $http_client->post('/api/v1/project/search', [], $payload)->send();
        } catch (ServerErrorResponseException $e) {
            $this->fail("API Error: ".$e->getResponse()->getBody());
        }

        $this->assertEquals($response_code, $response->getStatusCode());

        return new EntityCollection($serializer->deserialize(
            $response->getBody(),
            'ArrayCollection<Hyperion\Dbal\Entity\Project>',
            'json'
        ));
    }

}
