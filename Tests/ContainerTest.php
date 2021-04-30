<?php

namespace OAuth2\ServerBundle\Tests;

use OAuth2\Request;
use OAuth2\Response;
use OAuth2\Server;
use OAuth2\ServerBundle\Manager\ClientManager;
use OAuth2\ServerBundle\Manager\ScopeManager;
use PHPUnit\Framework\TestCase;

class ContainerTest extends TestCase
{
    public function testOpenIdConfig()
    {
        $container = ContainerLoader::buildTestContainer();
        /** @var Server $server */
        $server = $container->get(Server::class);

        $this->assertTrue($server->getConfig('use_openid_connect'));
        $this->assertNotNull($server->getStorage('public_key'));

        $clientManager = $container->get(ClientManager::class);
        $scopeManager = $container->get(ScopeManager::class);

        $clientId = 'test-client-' . rand();
        $redirectUri = 'http://brentertainment.com';
        $scope  = 'openid';

        $scopeManager->createScope($scope, '');
        $clientManager->createClient(
          $clientId,
          explode(',', $redirectUri),
          array(),
          explode(',', $scope)
        );

        $server->getStorage('public_key')->keys['public_key'] = file_get_contents(__DIR__.'/../vendor/bshaffer/oauth2-server-php/test/config/keys/id_rsa.pub');
        $server->getStorage('public_key')->keys['private_key'] = file_get_contents(__DIR__.'/../vendor/bshaffer/oauth2-server-php/test/config/keys/id_rsa');

        $request = new Request(array(
            'client_id'     => $clientId,
            'redirect_uri'  => 'http://brentertainment.com',
            'response_type' => 'code',
            'scope'         => 'openid',
            'state'         => 'xyz',
        ));

        $response = new Response();
        $server->handleAuthorizeRequest($request, $response, true, $clientId);
        $parts = parse_url($response->getHttpHeader('Location'));
        parse_str($parts['query'], $query);
        $code = $server->getStorage('authorization_code')->getAuthorizationCode($query['code']);

        $this->assertArrayHasKey('idToken', $code);
    }
}
