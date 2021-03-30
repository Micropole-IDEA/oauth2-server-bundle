<?php

namespace OAuth2\ServerBundle\Tests\Entity;

use Exception;
use OAuth2\ServerBundle\Tests\ContainerLoader;
use OAuth2\ServerBundle\Entity\User;
use PHPUnit\Framework\TestCase;

/**
 * Class UserTest
 */
class UserTest extends TestCase
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

            $user = new User();
            $name = 'test-user-' . rand();
            $user->setUsername($name);
            $user->setPassword('very-secure');
            $user->setSalt(sha1(time()));

            $emn->persist($user);
            $emn->flush();

            $stored = $emn->find('OAuth2\ServerBundle\Entity\User', array('username' => $name));

            $this->assertNotNull($stored);
            $this->assertEquals($name, $stored->getUsername());
            $this->assertEquals($user->getPassword(), $stored->getPassword());
            $this->assertEquals($user->getSalt(), $stored->getSalt());
        } catch (Exception $exception) {
            throw $exception;
        }
    }
}
