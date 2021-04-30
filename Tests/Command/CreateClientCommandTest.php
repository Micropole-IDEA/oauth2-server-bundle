<?php

namespace OAuth2\ServerBundle\Tests\Command;

use Exception;
use OAuth2\ServerBundle\Manager\ScopeManager;
use OAuth2\ServerBundle\Storage\ClientCredentials;
use OAuth2\ServerBundle\Storage\Scope;
use OAuth2\ServerBundle\Tests\ContainerLoader;
use OAuth2\ServerBundle\Command\CreateClientCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * Class CreateClientCommandTest
 */
class CreateClientCommandTest extends TestCase
{
    /**
     * testCreateClientWithInvalidScope
     *
     * @throws Exception
     */
    public function testCreateClientWithInvalidScope()
    {
        try {
            $container = ContainerLoader::buildTestContainer();
            $command = $container->get(CreateClientCommand::class);


            $clientId = 'Client-ID-' . rand();
            $redirectUris = 'http://brentertainment.com';
            $grantTypes = 'authorization_code,client_credentials';
            $scopes = 'fakescope';

            $input = new ArgvInput(array('command', $clientId, $redirectUris, $grantTypes, $scopes));
            $output = new BufferedOutput();

            $statusCode = $command->run($input, $output);

            $this->assertEquals(1, $statusCode);
            $this->assertTrue(
                false !== strpos($output->fetch(), 'Scope not found, please create it first')
            );
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    /**
     * testCreateClient
     *
     * @throws Exception
     */
    public function testCreateClient()
    {
        try {
            $container = ContainerLoader::buildTestContainer();
            $command = $container->get(CreateClientCommand::class);
            $clientId = 'Client-ID-' . rand();
            $redirectUris = 'http://brentertainment.com';
            $grantTypes = 'authorization_code,client_credentials';
            $scope = 'scope1';

            // ensure the scope exists
            $scopeStorage = $container->get(Scope::class);
            if (!$scopeStorage->scopeExists($scope)) {
                $scopeManager = $container->get(ScopeManager::class);
                $scopeManager->createScope($scope, 'test scope');
            }

            $input = new ArgvInput(array('command', $clientId, $redirectUris, $grantTypes, $scope));
            $output = new BufferedOutput();

            $statusCode = $command->run($input, $output);
            $this->assertEquals(0, $statusCode, $output->fetch());

            // verify client details have been stored
            $storage = $container->get(ClientCredentials::class);
            $client  = $storage->getClientDetails($clientId);
            $this->assertNotNull($client);
            $this->assertEquals($redirectUris, $client['redirect_uri']);
            $this->assertEquals(explode(',', $grantTypes), $client['grant_types']);

            // verify client scope has been stored
            $clientScope = $storage->getClientScope($clientId);
            $this->assertEquals($scope, $clientScope);
        } catch (Exception $exception) {
            throw $exception;
        }
    }
}
