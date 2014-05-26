<?php

namespace Hyperion\ApiBundle\Tests\Controller;

use Eloquent\Enumeration\AbstractEnumeration;
use FOS\RestBundle\Util\Codes;
use Guzzle\Http\Client;
use Guzzle\Http\Exception\BadResponseException;
use Guzzle\Http\Exception\ClientErrorResponseException;
use Guzzle\Inflection\Inflector;
use Hyperion\ApiBundle\Collection\EntityCollection;
use Hyperion\ApiBundle\Entity\HyperionEntityInterface;
use Hyperion\Dbal\Enum\BakeStatus;
use Hyperion\Dbal\Enum\Packager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CrudControllerTest extends WebTestCase
{

    const BASE_URL = 'http://api.hyperion.dev';

    protected static $created = [];

    /**
     * @small
     * @dataProvider entityProvider
     */
    public function testEntityGet404($entity, $payload)
    {
        $http_client = new Client(self::BASE_URL);

        try {
            $http_client->get('/api/v1/'.$entity.'/1234567')->send();
            $this->fail("Request succeeded when it shouldn't have");
        } catch (ClientErrorResponseException $e) {
            $this->assertEquals(404, $e->getResponse()->getStatusCode());
        }
    }

    /**
     * @small
     * @dataProvider entityProvider
     */
    public function testEntityDelete404($entity, $payload)
    {
        $http_client = new Client(self::BASE_URL);

        try {
            $http_client->delete('/api/v1/'.$entity.'/1234567')->send();
            $this->fail("Request succeeded when it shouldn't have");
        } catch (ClientErrorResponseException $e) {
            $this->assertEquals(404, $e->getResponse()->getStatusCode());
        }
    }

    /**
     * @medium
     * @dataProvider entityProvider
     */
    public function testEntityCrud($entity, $payload)
    {
        $class_name  = "Hyperion\\Dbal\\Entity\\".Inflector::getDefault()->camel($entity);
        $serializer  = static::createClient()->getContainer()->get('jms_serializer');
        $http_client = new Client(self::BASE_URL);
        $response    = null;
        $post_data   = [];

        // We may have referenced previously created entities -
        foreach ($payload as $key => $value) {
            if ($value{0} == '@') {
                $value = self::$created[substr($value, 1)];
            }

            if (is_object($value)) {
                $value = $serializer->serialize($value, 'json');
            }

            $post_data[$key] = $value;
        }

        // CREATE
        try {
            $response = $http_client->post('/api/v1/'.$entity.'/new', [], $post_data)->send();
            $this->assertEquals(Codes::HTTP_CREATED, $response->getStatusCode());

        } catch (BadResponseException $e) {
            $this->fail('Server returned '.$e->getResponse()->getStatusCode().': '.$e->getResponse()->getBody()."\nPayload:\n".print_r($post_data, true)."\n");
        }

        $created = $serializer->deserialize(
            $response->getBody(),
            $class_name,
            'json'
        );

        $this->assertTrue(is_int($created->getId()));
        $this->assertGreaterThan(0, $created->getId());
        self::$created[$entity] = $created->getId();

        // RETRIEVE
        $response = $http_client->get('/api/v1/'.$entity.'/'.$created->getId())->send();
        $this->assertEquals(Codes::HTTP_OK, $response->getStatusCode());

        $retrieved = $serializer->deserialize(
            $response->getBody(),
            $class_name,
            'json'
        );

        $this->assertEquals($created->getId(), $retrieved->getId());

        foreach ($post_data as $key => $value) {
            $getFn = 'get'.Inflector::getDefault()->camel($key);
            $retrieved_val = $retrieved->$getFn();

            // Some smarts are applied to the entities, convert them back to their raw values for testing
            if ($retrieved_val instanceof AbstractEnumeration) {
                $retrieved_val = $retrieved_val->value();
            } elseif (is_array($retrieved_val)) {
                $retrieved_val = json_encode($retrieved_val);
            }

            $this->assertEquals($value, $retrieved_val, 'Property match for `'.$key.'`');
        }

        // RETRIEVE ALL
        $response = $http_client->get('/api/v1/'.$entity.'/all')->send();
        $this->assertEquals(Codes::HTTP_OK, $response->getStatusCode());

        /** @var $retrieved EntityCollection */
        $retrieved_all = new EntityCollection($serializer->deserialize(
            $response->getBody(),
            'ArrayCollection<'.$class_name.'>',
            'json'
        ));

        $this->assertGreaterThan(0, $retrieved_all->count());

        $item = $retrieved_all->getById($created->getId());
        $this->assertEquals($created->getId(), $item->getId());

        // UPDATE
        $update_data = $post_data;
        $update_data['name'] = 'Updated name #'.rand(100, 999);

        try {
            $response = $http_client->put('/api/v1/'.$entity.'/'.$created->getId(), [], $update_data)->send();
            $this->assertEquals(Codes::HTTP_OK, $response->getStatusCode());

        } catch (BadResponseException $e) {
            $this->fail('Server returned '.$e->getResponse()->getStatusCode().': '.$e->getResponse()->getBody());
        }

        $updated = $serializer->deserialize(
            $response->getBody(),
            $class_name,
            'json'
        );

        $this->assertGreaterThan(0, $updated->getId());

    }

    /**
     * @medium
     * @depends testEntityCrud
     */
    public function testCleanup()
    {
        $this->cleanAllEntities();
    }


    /**
     * Delete everything in the database
     */
    protected function cleanAllEntities()
    {
        $entities = [
            'project',
            'distribution',
            'instance',
            'action',
            'repository',
            'proxy',
            'credential',
            'account',
        ];

        foreach ($entities as $entity) {
            $this->cleanEntity($entity);
        }
    }

    /**
     * Remove all objects from a table
     *
     * @param $entity
     */
    protected function cleanEntity($entity)
    {
        $class_name  = "Hyperion\\Dbal\\Entity\\".Inflector::getDefault()->camel($entity);
        $http_client = new Client(self::BASE_URL);
        $serializer  = static::createClient()->getContainer()->get('jms_serializer');

        $response = $http_client->get('/api/v1/'.$entity.'/all')->send();
        $this->assertEquals(Codes::HTTP_OK, $response->getStatusCode());

        $retrieved_all = new EntityCollection($serializer->deserialize(
            $response->getBody(),
            'ArrayCollection<'.$class_name.'>',
            'json'
        ));

        /** @var $item HyperionEntityInterface */
        foreach ($retrieved_all as $item) {
            $response = $http_client->delete('/api/v1/'.$entity.'/'.$item->getId())->send();
            $this->assertEquals(Codes::HTTP_OK, $response->getStatusCode());
        }
    }

    public function entityProvider()
    {
        $rand_id = rand(1000, 9999);
        return [
            [
                'account',
                [
                    'name' => 'Test Account #'.$rand_id,
                ]
            ],
            [
                'project',
                [
                    'name'                   => 'Test Project #'.$rand_id,
                    'account'                => '@account',
                    'bake_status'            => BakeStatus::UNBAKED,
                    'source_image_id'        => 'i-fake',
                    'packager'               => Packager::YUM,
                    'update_system_packages' => 0,
                    'packages'               => '[]',
                    'services'               => '[]',
                ]
            ],
        ];
    }

}
