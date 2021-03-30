<?php

namespace OAuth2\ServerBundle\Controller;

use OAuth2\HttpFoundationBridge\Request;
use OAuth2\HttpFoundationBridge\Response;
use OAuth2\ResponseInterface;
use OAuth2\Server;
use OAuth2\ServerBundle\Storage\Scope;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * AuthorizeController
 */
class AuthorizeController extends AbstractController
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
     * @var Scope
     */
    protected Scope $scopeStorage;

    /**
     * VerifyController constructor.
     *
     * @param Server   $server
     * @param Request  $request
     * @param Response $response
     * @param Scope    $scopeStorage
     */
    public function __construct(
        Server $server,
        Request $request,
        Response $response,
        Scope $scopeStorage
    ) {
        $this->server = $server;
        $this->request = $request;
        $this->response = $response;
        $this->scopeStorage = $scopeStorage;
    }

    /**
     * validateAuthorizeAction
     *
     * @return array
     *
     * @Route("/authorize", name="_authorize_validate", methods={"GET"})
     *
     * @Template("@OAuth2Server/Authorize/authorize.html.twig")
     */
    public function validateAuthorizeAction(): array
    {
        if (!$this->server->validateAuthorizeRequest($this->request, $this->response)) {
            return $this->server->getResponse();
        }

        // Get descriptions for scopes if available
        $scopes = array();
        foreach (explode(' ', $this->request->query->get('scope')) as $scope) {
            $scopes[] = $this->scopeStorage->getDescriptionForScope($scope);
        }

        $qs = array_intersect_key(
            $this->request->query->all(),
            array_flip(explode(' ', 'response_type client_id redirect_uri scope state nonce'))
        );

        return array('qs' => $qs, 'scopes' => $scopes);
    }

    /**
     * handleAuthorizeAction
     *
     * @return ResponseInterface
     *
     * @Route("/authorize", name="_authorize_handle", , methods={"POST"})
     */
    public function handleAuthorizeAction(): ResponseInterface
    {
        return $this->server->handleAuthorizeRequest($this->request, $this->response, true);
    }
}
