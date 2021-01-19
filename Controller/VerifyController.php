<?php

namespace OAuth2\ServerBundle\Controller;

use OAuth2\HttpFoundationBridge\Request;
use OAuth2\HttpFoundationBridge\Response;
use OAuth2\Server;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class VerifyController
 */
class VerifyController extends AbstractController
{
    /**
     * @var Server
     */
    protected Server $server;

    /**
     * @var Request
     */
    protected Request $request;

    /**
     * @var Response
     */
    protected Response $response;

    /**
     * VerifyController constructor.
     *
     * @param Server   $server
     * @param Request  $request
     * @param Response $response
     */
    public function __construct(
        Server $server,
        Request $request,
        Response $response
    ) {
        $this->server = $server;
        $this->request = $request;
        $this->response = $response;
    }

    /**
     * This is called with an access token, details
     * about the access token are then returned.
     * Used for verification purposes.
     *
     * @return JsonResponse
     *
     * @Route("/verify", name="_verify_token")
     */
    public function verifyAction(): JsonResponse
    {
        if (!$this->server->verifyResourceRequest($this->request, $this->response)) {
            return $this->server->getResponse();
        }

        $tokenData = $this->server->getAccessTokenData($this->request, $this->response);

        return new JsonResponse($tokenData);
    }
}
