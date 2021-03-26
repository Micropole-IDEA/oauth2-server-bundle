<?php

namespace OAuth2\ServerBundle\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use OAuth2\ServerBundle\Entity\Scope;

/**
 * Class ScopeManager
 */
class ScopeManager implements ScopeManagerInterface
{
    /**
     * @var EntityManager
     */
    private EntityManager $emn;

    /**
     * ScopeManager constructor.
     *
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->emn = $entityManager;
    }

    /**
     * createScope
     *
     * @param string      $scope
     * @param string|null $description
     *
     * @return Scope
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function createScope(string $scope, ?string $description = null): Scope
    {
        if ($scopeObject = $this->findScopeByScope($scope)) {

          return $scopeObject;
        }

        $scopeObject = new \OAuth2\ServerBundle\Entity\Scope();
        $scopeObject->setScope($scope);
        $scopeObject->setDescription($description);

        // Store Scope
        $this->emn->persist($scopeObject);
        $this->emn->flush();

        return $scopeObject;
    }

    /**
     * Find a single scope by the scope
     *
     * @param string $scope
     *
     * @return object|null
     */
    public function findScopeByScope(string $scope): ?object
    {
        return $this->emn->getRepository('OAuth2ServerBundle:Scope')->find($scope);
    }

    /**
     * Find all the scopes by an array of scopes
     *
     * @param array $scopes
     *
     * @return Scope[]
     */
    public function findScopesByScopes(array $scopes): array
    {
        return $this->emn->getRepository('OAuth2ServerBundle:Scope')
            ->createQueryBuilder('a')
            ->where('a.scope in (?1)')
            ->setParameter(1, $scopes)
            ->getQuery()->getResult();
    }
}
