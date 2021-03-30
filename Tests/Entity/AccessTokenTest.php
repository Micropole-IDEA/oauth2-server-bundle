<?php

namespace OAuth2\ServerBundle\Tests\Entity;

use DateTime;
use Exception;
use OAuth2\ServerBundle\Tests\ContainerLoader;
use OAuth2\ServerBundle\Entity\AccessToken;
use PHPUnit\Framework\TestCase;

/**
 * Class AccessTokenTest
 */
class AccessTokenTest extends TestCase
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

            $accessToken = new AccessToken();
            $accessToken->setToken($token = 'test-token-' . rand());
            $accessToken->setExpires(new DateTime('+10 minutes'));

            $emn->persist($accessToken);
            $emn->flush();

            $stored = $emn->find('OAuth2\ServerBundle\Entity\AccessToken', array('token' => $token));

            $this->assertNotNull($stored);
            $this->assertEquals($token, $stored->getToken());
            $this->assertEquals($accessToken->getExpires(), $stored->getExpires());
        } catch (Exception $exception) {
            throw $exception;
        }
    }
}
