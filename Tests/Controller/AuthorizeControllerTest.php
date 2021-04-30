<?php

namespace OAuth2\ServerBundle\Tests\Controller;

use OAuth2\HttpFoundationBridge\Request;
use OAuth2\ServerBundle\Manager\ClientManager;
use OAuth2\ServerBundle\Tests\ContainerLoader;
use OAuth2\ServerBundle\Controller\AuthorizeController;
use PHPUnit\Framework\TestCase;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

/**
 * Class AuthorizeControllerTest
 */
class AuthorizeControllerTest extends TestCase
{
    /**
     * testOpenIdConfig
     *
     * @throws \Exception
     */
    public function testOpenIdConfig()
    {
        try {
            $container = ContainerLoader::buildTestContainer();

            $clientManager = $container->get(ClientManager::class);

            $clientId = 'test-client-' . rand();
            $redirectUri = 'http://brentertainment.com';
            $scope = 'openid';

            $clientManager->createClient(
                $clientId,
                explode(',', $redirectUri),
                array(),
                explode(',', $scope)
            );

            $request = new Request(
                array(
                   'client_id'     => $clientId,
                   'response_type' => 'code',
                   'scope'         => 'openid',
                   'state'         => 'xyz',
                   'foo'           => 'bar',
                   'nonce'         => '123',
                )
            );
            $stack = $container->get('request_stack');
            $stack->push($request);
            $container->set(Request::class, $request);
            $controller = $container->get(AuthorizeController::class);
            $params = $controller->validateAuthorizeAction();

            $this->assertArrayHasKey('nonce', $params['qs'], 'optional included param');
            $this->assertArrayNotHasKey('foo', $params['qs'], 'invalid included param');
            $this->assertArrayNotHasKey('redirect_uri', $params['qs'], 'optional excluded param');

            $loader = new FilesystemLoader(__DIR__ . '/../../Resources/views');
            $twig = new Environment($loader);
            $template = $twig->load('Authorize/authorize.html.twig');
            $html = $template->render($params);
            $this->assertStringContainsString(htmlentities(http_build_query($params['qs'])), $html);
        } catch (\Exception $exception) {
            throw $exception;
        }
    }
}
