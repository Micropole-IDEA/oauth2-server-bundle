<?php

namespace OAuth2\ServerBundle\Tests\Entity;

use Exception;
use OAuth2\ServerBundle\Tests\ContainerLoader;
use OAuth2\ServerBundle\Entity\Client;
use PHPUnit\Framework\TestCase;

/**
 * Class ClientTest
 */
class ClientTest extends TestCase
{
    /**
     * testCreate
     *
     * @throws Exception
     */
    public function testCreate()
    {
        try {
            $container = ContainerLoader::buildTestContainer();
            $emn = $container->get('doctrine.orm.entity_manager');

            $client = new Client();
            $clientId = 'This Is My Client ID ' . rand();
            $client->setClientId($clientId);
            $client->setClientSecret('very-secure');
            $client->setRedirectUri(array('http://brentertainment.com'));

            $emn->persist($client);
            $emn->flush();

            $stored = $emn->find('OAuth2\ServerBundle\Entity\Client', array('clientId' => $clientId));

            $this->assertNotNull($stored);
            $this->assertEquals($clientId, $stored->getClientId());
            $this->assertEquals($client->getClientSecret(), $stored->getClientSecret());
            $this->assertEquals($client->getRedirectUri(), $stored->getRedirectUri());
        } catch (Exception $exception) {
            throw $exception;
        }
    }
}
