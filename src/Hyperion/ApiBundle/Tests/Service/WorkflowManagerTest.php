<?php
namespace Hyperion\ApiBundle\Tests\Service;

use Hyperion\ApiBundle\Entity\Account;
use Hyperion\ApiBundle\Entity\Credential;
use Hyperion\ApiBundle\Entity\Project;
use Hyperion\Dbal\Enum\BakeStatus;
use Hyperion\Dbal\Enum\Packager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class WorkflowManagerTest extends WebTestCase
{

    /**
     * @large
     * @group integration
     */
    public function testBake()
    {
        $client = static::createClient();
        $wfm    = $client->getContainer()->get('hyperion.workflow_manager');
        $em     = $client->getContainer()->get('doctrine.orm.entity_manager');
        $creds  = $client->getContainer()->getParameter('hyperion.workflow.test');

        $account = new Account();
        $account->setName('Bakery Test Account');
        $em->persist($account);

        $credential = new Credential();
        $credential->setAccount($account);
        $credential->setAccessKey($creds['access_key']);
        $credential->setSecret($creds['secret']);
        $credential->setProvider(constant('Hyperion\Dbal\Enum\Provider::'.$creds['provider']));
        $credential->setRegion($creds['region']);
        $em->persist($credential);

        $project = new Project();
        $project->setAccount($account);
        $project->setName('Bakery Test Project');
        $project->setBakeStatus(BakeStatus::UNBAKED);
        $project->setPackager(Packager::YUM);
        $project->setPackages("['httpd','mysql-server','screen','vim','php']");
        $project->setServices("['mysqld','httpd']");
        $project->setTestCredential($credential);
        $project->setProdCredential($credential);
        $project->setScript('touch /tmp/baked');
        $project->setUpdateSystemPackages(1);
        $project->setSourceImageId('ami-3b4bd301');
        $project->setZones(json_encode(['ap-southeast-2a', 'ap-southeast-2b']));
        $project->setKeysProd(json_encode([]));
        $project->setKeysTest(json_encode(['test-sydney'])); // TODO: move to params
        $project->setFirewallsProd(json_encode([]));
        $project->setFirewallsTest(json_encode(['test'])); // TODO: move to params
        $project->setTagsProd(json_encode([]));
        $project->setTagsTest(json_encode(['env' => 'test']));
        $em->persist($project);

        $em->flush();

        $wf_id = $wfm->bake($project);
        $this->assertGreaterThan(0, $wf_id);

    }
}