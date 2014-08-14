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
use Hyperion\Dbal\Entity\Account;
use Hyperion\Dbal\Entity\Action;
use Hyperion\Dbal\Entity\Environment;
use Hyperion\Dbal\Entity\HyperionEntity;
use Hyperion\Dbal\Entity\Project;
use Hyperion\Dbal\Entity\Repository;
use Hyperion\Dbal\Enum\ActionState;
use Hyperion\Dbal\Enum\ActionType;
use Hyperion\Dbal\Enum\BakeStatus;
use Hyperion\Dbal\Enum\EnvironmentType;
use Hyperion\Dbal\Enum\Packager;
use Hyperion\Dbal\Enum\RepositoryType;
use Hyperion\Dbal\Enum\Tenancy;
use JMS\Serializer\SerializerBuilder;
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
            if (is_array($value)) {
                $value = json_encode($value);
            } elseif (is_object($value)) {
                $value = $serializer->serialize($value, 'json');
            } elseif ($value{0} == '@') {
                $value = self::$created[substr($value, 1)];
            }

            $post_data[$key] = $value;
        }

        // CREATE
        try {
            $response = $http_client->post('/api/v1/entity/'.$entity.'/new', [], $post_data)->send();
            $this->assertEquals(Codes::HTTP_CREATED, $response->getStatusCode());

        } catch (BadResponseException $e) {
            $this->fail(
                'Server returned '.$e->getResponse()->getStatusCode().': '.$e->getResponse()->getBody()."\nPayload:\n".
                print_r($post_data, true)."\n"
            );
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
        $response = $http_client->get('/api/v1/entity/'.$entity.'/'.$created->getId())->send();
        $this->assertEquals(Codes::HTTP_OK, $response->getStatusCode());

        $retrieved = $serializer->deserialize(
            $response->getBody(),
            $class_name,
            'json'
        );

        $this->assertEquals($created->getId(), $retrieved->getId());

        foreach ($post_data as $key => $value) {
            $getFn         = 'get'.Inflector::getDefault()->camel($key);
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
        $response = $http_client->get('/api/v1/entity/'.$entity.'/all')->send();
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
        $update_data         = $post_data;
        $update_data['name'] = 'Updated name #'.rand(100, 999);

        try {
            $response = $http_client->put('/api/v1/entity/'.$entity.'/'.$created->getId(), [], $update_data)->send();
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
     */
    public function testXRef()
    {
        $http_client = new Client(self::BASE_URL);
        $serializer  = SerializerBuilder::create()->build();

        $account = new Account();
        $account->setName("Test account");
        $this->createEntity($account);

        $project = new Project();
        $project->setName("Test Project");
        $project->setAccount($account->getId());
        $project->setBakeStatus(BakeStatus::UNBAKED());
        $project->setSourceImageId('-');
        $project->setPackager(Packager::YUM());
        $project->setUpdateSystemPackages(true);
        $project->setZones([]);
        $project->setPackages([]);
        $project->setServices([]);
        $this->createEntity($project);

        $repo = new Repository();
        $repo->setAccount($account->getId());
        $repo->setName("Test repo");
        $repo->setCheckoutDirectory('/tmp');
        $repo->setUrl('git@github.com:stuff.git');
        $repo->setType(RepositoryType::GIT());
        $this->createEntity($repo);

        $response = $http_client->put('/api/v1/entity/project/'.$project->getId().'/repository/'.$repo->getId(), [], '')->send();
        $this->assertEquals(Codes::HTTP_NO_CONTENT, $response->getStatusCode());

        $response = $http_client->get('/api/v1/entity/project/'.$project->getId().'/repository', [])->send();
        $this->assertEquals(Codes::HTTP_OK, $response->getStatusCode());

        $retrieved_all = new EntityCollection($serializer->deserialize(
            $response->getBody(),
            'ArrayCollection<'.get_class($repo).'>',
            'json'
        ));

        $this->assertEquals(1, count($retrieved_all));

        $project->setBakeStatus(BakeStatus::BAKING());

        $id = $project->getId();
        $project->setId(null);

        $payload = $serializer->serialize($project, 'json');
        $response = $http_client->put('/api/v1/entity/project/'.$id, [], $payload)->send();
        $this->assertEquals(Codes::HTTP_OK, $response->getStatusCode());

        $response = $http_client->get('/api/v1/entity/project/'.$id.'/repository', [])->send();
        $this->assertEquals(Codes::HTTP_OK, $response->getStatusCode());

        $retrieved_all = new EntityCollection($serializer->deserialize(
            $response->getBody(),
            'ArrayCollection<'.get_class($repo).'>',
            'json'
        ));

        $this->assertEquals(1, count($retrieved_all));

        $env = new Environment();
        $env->setName("Test environment");
        $env->setEnvironmentType(EnvironmentType::BAKERY());
        $env->setInstanceSize('m1.large');
        $env->setSshUser('ec2-user');
        $env->setSshPort(22);
        $env->setTenancy(Tenancy::DEDICATED());
        $env->setTags([]);
        $this->createEntity($env);

        $action = new Action();
        $action->setProject($project->getId());
        $action->setEnvironment($env->getId());
        $action->setActionType(ActionType::BAKE());
        $action->setState(ActionState::ACTIVE());
        $action->setOutput('');
        $action->setErrorMessage(null);
        $action->setPhase('Test');
        $this->createEntity($action);

    }

    /**
     * Create an entity on the API server
     *
     * @param HyperionEntity $entity
     * @return HyperionEntity
     */
    protected function createEntity(HyperionEntity &$entity)
    {
        $class_name = get_class($entity);
        $short_name = explode('\\', $class_name);
        $short_name = strtolower(array_pop($short_name));

        $http_client = new Client(self::BASE_URL);
        $serializer  = SerializerBuilder::create()->build();

        $payload = $serializer->serialize($entity, 'json');
        $response = $http_client->post('/api/v1/entity/'.$short_name.'/new', [], $payload)->send();
        $this->assertEquals(Codes::HTTP_CREATED, $response->getStatusCode());

        $created = $serializer->deserialize(
            $response->getBody(),
            $class_name,
            'json'
        );

        $this->assertGreaterThan(0, $created->getId());
        $entity = $created;

        return $created;
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
            'instance',
            'action',
            'environment',
            'distribution',
            'project',
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

        $response = $http_client->get('/api/v1/entity/'.$entity.'/all')->send();
        $this->assertEquals(Codes::HTTP_OK, $response->getStatusCode());

        $retrieved_all = new EntityCollection($serializer->deserialize(
            $response->getBody(),
            'ArrayCollection<'.$class_name.'>',
            'json'
        ));

        /** @var $item HyperionEntityInterface */
        foreach ($retrieved_all as $item) {
            $response = $http_client->delete('/api/v1/entity/'.$entity.'/'.$item->getId())->send();
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
                    'zones'                  => '[]',
                ]
            ],
            [
                'environment',
                [
                    'name'             => 'CI',
                    'project'          => '@project',
                    'environment_type' => EnvironmentType::TEST,
                    'tenancy'          => Tenancy::VPC,
                    'instance_size'    => 'm1.medium',
                    'tags'             => ['env' => 'test'],
                    'key_pairs'        => ['test'],
                    'firewalls'        => ['ci'],
                    'ssh_port'         => 22,
                    'ssh_user'         => 'ec2-user',
                ]
            ],
        ];
    }

}
