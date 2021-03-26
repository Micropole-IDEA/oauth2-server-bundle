<?php

namespace OAuth2\ServerBundle\Entity;

use DateTime;

/**
 * Class AbstractToken
 */
abstract class AbstractToken
{
    /**
     * @var string
     */
    protected string $token;

    /**
     * @var ?string
     */
    protected ?string $userId;

    /**
     * @var DateTime
     */
    protected DateTime $expires;

    /**
     * @var string
     */
    protected string $scope;

    /**
     * @var ?Client
     */
    protected ?Client $client;

    /**
     * Set token
     *
     * @param string $token
     *
     * @return AbstractToken
     */
    public function setToken(string $token): AbstractToken
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get token
     *
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * Set userId
     *
     * @param ?string $userId
     *
     * @return AbstractToken
     */
    public function setUserId(?string $userId): AbstractToken
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get userId
     *
     * @return ?string
     */
    public function getUserId(): ?string
    {
        return $this->userId;
    }

    /**
     * Set expires
     *
     * @param DateTime|int $expires
     *
     * @return AbstractToken
     */
    public function setExpires($expires): AbstractToken
    {
        if (!$expires instanceof DateTime) {
            $dateTime = new DateTime();
            $dateTime->setTimestamp($expires);
            $expires = $dateTime;
        }

        $this->expires = $expires;

        return $this;
    }

    /**
     * Get expires
     *
     * @return DateTime
     */
    public function getExpires(): DateTime
    {
        return $this->expires;
    }

    /**
     * Set scope
     *
     * @param string $scope
     *
     * @return AbstractToken
     */
    public function setScope(string $scope): AbstractToken
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
     * Set client
     *
     * @param Client|null $client
     *
     * @return AbstractToken
     */
    public function setClient(?Client $client = null): AbstractToken
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Get client
     *
     * @return ?Client
     */
    public function getClient(): ?Client
    {
        return $this->client;
    }
}
