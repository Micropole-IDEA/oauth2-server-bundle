<?php

namespace OAuth2\ServerBundle\Entity;

/**
 * Client
 */
class Client
{
    /**
     * @var string
     */
    private string $clientId;

    /**
     * @var string
     */
    private string $clientSecret;

    /**
     * @var array
     */
    private array $redirectUri;

    /**
     * @var array
     */
    private array $grantTypes;

    /**
     * @var ?ClientPublicKey
     */
    private ?ClientPublicKey $publicKey = null;

    /**
     * @var array
     */
    private array $scopes;

    /**
     * Set clientId
     *
     * @param string $clientId
     *
     * @return Client
     */
    public function setClientId(string $clientId): Client
    {
        $this->clientId = $clientId;

        return $this;
    }

    /**
     * Get clientId
     *
     * @return string
     */
    public function getClientId(): string
    {
        return $this->clientId;
    }

    /**
     * Set clientSecret
     *
     * @param string $clientSecret
     *
     * @return Client
     */
    public function setClientSecret(string $clientSecret): Client
    {
        $this->clientSecret = $clientSecret;

        return $this;
    }

    /**
     * Get clientSecret
     *
     * @return string
     */
    public function getClientSecret(): string
    {
        return $this->clientSecret;
    }

    /**
     * Set redirectUri
     *
     * @param array $redirectUri
     *
     * @return Client
     */
    public function setRedirectUri(array $redirectUri): Client
    {
        $this->redirectUri = $redirectUri;

        return $this;
    }

    /**
     * Get redirect_uri
     *
     * @return array
     */
    public function getRedirectUri(): array
    {
        return $this->redirectUri;
    }

    /**
     * Set grant_types
     *
     * @param array $grantTypes
     *
     * @return Client
     */
    public function setGrantTypes(array $grantTypes): Client
    {
        $this->grantTypes = $grantTypes;

        return $this;
    }

    /**
     * Get grantTypes
     *
     * @return array
     */
    public function getGrantTypes(): array
    {
        return $this->grantTypes;
    }

    /**
     * Set scopes
     *
     * @param array $scopes
     *
     * @return Client
     */
    public function setScopes(array $scopes): Client
    {
        $this->scopes = $scopes;

        return $this;
    }

    /**
     * Get scopes
     *
     * @return array
     */
    public function getScopes(): array
    {
        return $this->scopes;
    }

    /**
     * Set public key
     *
     * @param ClientPublicKey|null $publicKey
     *
     * @return Client
     */
    public function setPublicKey(?ClientPublicKey $publicKey = null): Client
    {
        $this->publicKey = $publicKey;

        return $this;
    }

    /**
     * Get public key
     *
     * @return ClientPublicKey
     */
    public function getPublicKey(): ?ClientPublicKey
    {
        return $this->publicKey;
    }
}
