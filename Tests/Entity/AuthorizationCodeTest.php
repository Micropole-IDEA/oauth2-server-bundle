<?php

namespace OAuth2\ServerBundle\Tests\Entity;

use OAuth2\ServerBundle\Tests\ContainerLoader;
use OAuth2\ServerBundle\Entity\AuthorizationCode;
use PHPUnit\Framework\TestCase;

/**
 * Class AuthorizationCodeTest
 */
class AuthorizationCodeTest extends TestCase
{
    /**
     * testCreate
     *
     * @throws \Exception
     */
    public function testCreate()
    {
        try {
            $container = ContainerLoader::buildTestContainer();
            $emn = $container->get('doctrine.orm.entity_manager');

            $authcode = new AuthorizationCode();
            $authcode->setCode($code = 'test-code-' . rand());
            $authcode->setExpires(new \DateTime('+10 minutes')); // ten minutes from now
            $authcode->setRedirectUri('http://brentertainment.com');

            $emn->persist($authcode);
            $emn->flush();

            $stored = $emn->find('OAuth2\ServerBundle\Entity\AuthorizationCode', array('code' => $code));

            $this->assertNotNull($stored);
            $this->assertEquals($code, $stored->getCode());
            $this->assertEquals($authcode->getExpires(), $stored->getExpires());
            $this->assertEquals($authcode->getRedirectUri(), $stored->getRedirectUri());
        } catch (\Exception $exception) {
            throw $exception;
        }
    }
}
