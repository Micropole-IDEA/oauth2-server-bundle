<?php

namespace OAuth2\ServerBundle\Tests\Entity;

use Exception;
use OAuth2\ServerBundle\Tests\ContainerLoader;
use OAuth2\ServerBundle\Entity\Scope;
use PHPUnit\Framework\TestCase;

/**
 * Class ScopeTest
 */
class ScopeTest extends TestCase
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

            $scope = new Scope();
            $name = 'test-scope-' . rand();
            $scope->setScope($name);
            $scope->setDescription('A Scope for Testing');

            $emn->persist($scope);
            $emn->flush();

            $stored = $emn->find('OAuth2\ServerBundle\Entity\Scope', array('scope' => $name));

            $this->assertNotNull($stored);
            $this->assertEquals($name, $stored->getScope());
            $this->assertEquals($scope->getDescription(), $stored->getDescription());
        } catch (Exception $exception) {
            throw $exception;
        }
    }
}
