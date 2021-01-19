<?php

namespace OAuth2\ServerBundle\Storage;

use OAuth2\Storage\ScopeInterface;
use OAuth2\ServerBundle\Manager\ScopeManagerInterface;
use Doctrine\ORM\EntityManager;

/**
 * Class Scope
 */
class Scope implements ScopeInterface
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
     * Scope constructor.
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
     * Check if the provided scope exists.
     *
     * @param string      $scope    A space-separated string of scopes.
     * @param string|null $clientId The requesting client.
     *
     * @return bool TRUE if it exists, FALSE otherwise.
     */
    public function scopeExists($scope, $clientId = null): bool
    {
        $scopes = explode(' ', $scope);
        if ($clientId) {
            $client = $this->emn->getRepository('OAuth2ServerBundle:Client')->find($clientId);

            if (!$client) {
                return false;
            }

            $valid_scopes = $client->getScopes();

            foreach ($scopes as $scope) {
                if (!in_array($scope, $valid_scopes)) {
                    return false;
                }
            }

            return true;
        }

        $valid_scopes = $this->smn->findScopesByScopes($scopes);

        return count($valid_scopes) === count($scopes);
    }

    /**
     * The default scope to use in the event the client
     * does not request one. By returning "false", a
     * request_error is returned by the server to force a
     * scope request by the client. By returning "null",
     * opt out of requiring scopes
     *
     * @param string|null $clientId The requesting client.
     *
     * @return bool
     * string representation of default scope, null if
     * scopes are not defined, or false to force scope
     * request by the client
     *
     * ex:
     *     'default'
     * ex:
     *     null
     */
    public function getDefaultScope($clientId = null): bool
    {
        return false;
    }

    /**
     * Gets the description of a given scope key, if
     * available, otherwise the key is returned.
     *
     * @param string $scope A space-separated string of scopes.
     *
     * @return string description of the scope key.
     */
    public function getDescriptionForScope($scope): string
    {
        $scopeObject = $this->smn->findScopeByScope($scope);

        if (!$scopeObject) {
            return $scope;
        }

        return $scopeObject->getDescription();
    }
}
