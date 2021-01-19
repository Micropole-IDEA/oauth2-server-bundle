<?php

namespace OAuth2\ServerBundle\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use OAuth2\ServerBundle\Entity\Client;
use OAuth2\ServerBundle\Exception\ScopeNotFoundException;

/**
 * Class ClientManager
 */
class ClientManager
{
    /**
     * @var EntityManager
     */
    private EntityManager $emn;

    /**
     * @var ScopeManagerInterface
     */
    private ScopeManagerInterface $smn;

    /**
     * ClientManager constructor.
     *
     * @param EntityManager         $entityManager
     * @param ScopeManagerInterface $scopeManager
     */
    public function __construct(EntityManager $entityManager, ScopeManagerInterface $scopeManager)
    {
        $this->emn = $entityManager;
        $this->smn = $scopeManager;
    }

    /**
     * createClient
     *
     * @param string $identifier
     * @param array $redirectUris
     * @param array $grantType
     * @param array $scopes
     *
     * @return Client
     *
     * @throws ScopeNotFoundException
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function createClient(
        string $identifier,
        array $redirectUris = array(),
        array $grantType = array(),
        array $scopes = array()
    ): Client {
        $client = new Client();
        $client->setClientId($identifier);
        $client->setClientSecret($this->generateSecret());
        $client->setRedirectUri($redirectUris);
        $client->setGrantTypes($grantType);

        // Verify scopes
        foreach ($scopes as $scope) {
            // Get Scope
            $scopeObject = $this->smn->findScopeByScope($scope);
            if (!$scopeObject) {
                throw new ScopeNotFoundException();
            }
        }

        $client->setScopes($scopes);

        // Store Client
        $this->emn->persist($client);
        $this->emn->flush();

        return $client;
    }

    /**
     * generateSecret
     *
     * @return string
     */
    protected function generateSecret(): string
    {
        return base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
    }
}
