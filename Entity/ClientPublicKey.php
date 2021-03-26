<?php

namespace OAuth2\ServerBundle\Entity;

/**
 * Client
 */
class ClientPublicKey
{
    /**
     * @var ?Client
     */
    private ?Client $client;

    /**
     * @var string
     */
    private string $publicKey;

    /**
     * @var string
     */
    private string $clientId;

    /**
     * Set client
     *
     * @param ?Client $client
     *
     * @return ClientPublicKey
     */
    public function setClient(?Client $client = null): ClientPublicKey
    {
        $this->client = $client;
        $this->clientId = $client->getClientId();

        return $this;
    }

    /**
     * Get client
     *
     * @return Client|null
     */
    public function getClient(): ?Client
    {
        return $this->client;
    }

    /**
     * Set public key
     *
     * @param string $publicKey
     *
     * @return ClientPublicKey
     */
    public function setPublicKey(string $publicKey): ClientPublicKey
    {
        $this->publicKey = $publicKey;

        return $this;
    }

    /**
     * Get public key
     *
     * @return string
     */
    public function getPublicKey(): string
    {
        return $this->publicKey;
    }
}
