<?php

namespace OAuth2\ServerBundle\Tests\Entity;

use Exception;
use OAuth2\ServerBundle\Tests\ContainerLoader;
use OAuth2\ServerBundle\Entity\RefreshToken;
use PHPUnit\Framework\TestCase;

/**
 * Class RefreshTokenTest
 */
class RefreshTokenTest extends TestCase
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

            $refreshToken = new RefreshToken();
            $token = 'test-token-' . rand();
            $refreshToken->setToken($token);
            $refreshToken->setExpires(new \DateTime('+10 minutes')); // ten minutes from now

            $emn->persist($refreshToken);
            $emn->flush();

            $stored = $emn->find('OAuth2\ServerBundle\Entity\RefreshToken', array('token' => $token));

            $this->assertNotNull($stored);
            $this->assertEquals($token, $stored->getToken());
            $this->assertEquals($refreshToken->getExpires(), $stored->getExpires());
        } catch (Exception $exception) {
            throw $exception;
        }
    }
}
