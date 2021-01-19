<?php

namespace OAuth2\ServerBundle\Entity;

/**
 * Class Scope
 */
class Scope
{
    /**
     * @var string
     */
    private string $scope;

    /**
     * @var string
     */
    private string $description;

    /**
     * Set scope
     *
     * @param string $scope
     *
     * @return Scope
     */
    public function setScope(string $scope): Scope
    {
        $this->scope = $scope;

        return $this;
    }

    /**
     * Get scope
     *
     * @return string
     */
    public function getScope(): string
    {
        return $this->scope;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Scope
     */
    public function setDescription(string $description): Scope
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }
}
