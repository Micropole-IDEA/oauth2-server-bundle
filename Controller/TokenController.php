<?php

namespace OAuth2\ServerBundle\Controller;

use OAuth2\GrantType\AuthorizationCode;
use OAuth2\GrantType\ClientCredentials;
use OAuth2\GrantType\RefreshToken;
use OAuth2\GrantType\UserCredentials;
use OAuth2\HttpFoundationBridge\Response;
use OAuth2\ResponseInterface;
use OAuth2\Server;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use OAuth2\HttpFoundationBridge\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * TokenController
 */
class TokenController extends AbstractController
{
    /**
     * @var Server
     */
    protected Server $server;

    /**
     * @var ClientCredentials
     */
    protected ClientCredentials $clientCredential;

    /**
     * @var AuthorizationCode;
     */
    protected AuthorizationCode $authorizationCode;

    /**
     * @var RefreshToken
     */
    protected RefreshToken $refreshToken;

    /**
     * @var UserCredentials
     */
    protected UserCredentials $userCredentials;

    /**
     * @var Request
     */
    protected Request $request;

    /**
     * @var Response
     */
    protected Response $response;

    /**
     * TokenController constructor.
     *
     * @param Server            $server
     * @param AuthorizationCode $authorizationCode
     * @param RefreshToken      $refreshToken
     * @param UserCredentials   $userCredentials
     * @param ClientCredentials $clientCredential
     * @param Request           $request
     * @param Response          $response
     */
    public function __construct(
        Server $server,
        AuthorizationCode $authorizationCode,
        RefreshToken $refreshToken,
        UserCredentials $userCredentials,
        ClientCredentials $clientCredential,
        Request $request,
        Response $response
    ) {
        $this->server = $server;
        $this->authorizationCode = $authorizationCode;
        $this->refreshToken = $refreshToken;
        $this->userCredentials = $userCredentials;
        $this->clientCredential = $clientCredential;
        $this->request = $request;
        $this->response = $response;
    }

    /**
     * tokenAction
     *
     * @return ResponseInterface
     *
     * @Route("/token", name="_token")
     */
    public function tokenAction(): ResponseInterface
    {
        $this->server->addGrantType($this->clientCredential);
        $this->server->addGrantType($this->authorizationCode);
        $this->server->addGrantType($this->refreshToken);
        $this->server->addGrantType($this->userCredentials);

        return $this->server->handleTokenRequest($this->request, $this->response);
    }
}
