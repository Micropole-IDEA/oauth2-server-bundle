<?php

namespace OAuth2\ServerBundle\Entity;

use DateTime;

/**
 * AuthorizationCode
 */
class AuthorizationCode
{
    /**
     * @var string
     */
    private string $code;

    /**
     * @var DateTime
     */
    private DateTime $expires;

    /**
     * @var string
     */
    private string $userId;

    /**
     * @var array
     */
    private array $redirectUri;

    /**
     * @var string
     */
    private string $scope;

    /**
     * @var ?Client
     */
    private ?Client $client;

    /**
     * @var string
     */
    private string $idToken;

    /**
     * Set code
     *
     * @param string $code
     *
     * @return AuthorizationCode
     */
    public function setCode(string $code): AuthorizationCode
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * Set expires
     *
     * @param  DateTime|int $expires
     *
     * @return AuthorizationCode
     */
    public function setExpires($expires): AuthorizationCode
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
     * Set userId
     *
     * @param string $userId
     *
     * @return AuthorizationCode
     */
    public function setUserId(string $userId): AuthorizationCode
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get userId
     *
     * @return string
     */
    public function getUserId(): string
    {
        return $this->userId;
    }

    /**
     * Set redirectUri
     *
     * @param string $redirectUri
     *
     * @return AuthorizationCode
     */
    public function setRedirectUri(string $redirectUri): AuthorizationCode
    {
        $this->redirectUri = explode(' ', $redirectUri);

        return $this;
    }

    /**
     * Get redirectUri
     *
     * @return array
     */
    public function getRedirectUri(): array
    {
        return $this->redirectUri;
    }

    /**
     * Set scope
     *
     * @param string $scope
     *
     * @return AuthorizationCode
     */
    public function setScope(string $scope): AuthorizationCode
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
     * @return AuthorizationCode
     */
    public function setClient($client = null): AuthorizationCode
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

    /**
     * Get idToken
     *
     * @return string
     */
    public function getIdToken(): string
    {
      return $this->idToken;
    }

    /**
     * Set idToken
     *
     * @param string $idToken
     *
     * @return AuthorizationCode
     */
    public function setIdToken(string $idToken): AuthorizationCode
    {
        $this->idToken = $idToken;

        return $this;
    }
}
