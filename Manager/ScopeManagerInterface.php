<?php

namespace OAuth2\ServerBundle\Manager;

use OAuth2\ServerBundle\Entity\Scope;

/**
 * Interface ScopeManagerInterface
 */
interface ScopeManagerInterface
{
    /**
     * Creates a new scope
     *
     * @param string      $scope
     * @param string|null $description
     *
     * @return Scope
     */
    public function createScope(string $scope, ?string $description = null): Scope;

    /**
     * Find a single scope by the scope
     *
     * @param string $scope
     *
     * @return object|null
     */
    public function findScopeByScope(string $scope): ?object;

    /**
     * Find all the scopes by an array of scopes
     *
     * @param array $scopes
     *
     * @return Scope[]
     */
    public function findScopesByScopes(array $scopes): array;
}
