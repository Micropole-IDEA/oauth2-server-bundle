<?php

namespace OAuth2\ServerBundle\Storage;

use Doctrine\ORM\ORMException;
use OAuth2\Storage\AccessTokenInterface;
use Doctrine\ORM\EntityManager;
use OAuth2\ServerBundle\Entity\Client;
use OAuth2\ServerBundle\Entity\AccessToken as EntityAccessToken;

/**
 * Class AccessToken
 */
class AccessToken implements AccessTokenInterface
{
    /**
     * @var EntityManager
     */
    private EntityManager $emn;

    /**
     * AccessToken constructor.
     *
     * @param EntityManager $EntityManager
     */
    public function __construct(EntityManager $EntityManager)
    {
        $this->emn = $EntityManager;
    }

    /**
     * Look up the supplied oauth_token from storage.
     *
     * We need to retrieve access token data as we create and verify tokens.
     *
     * @param string $oauthToken oauth_token to be check with.
     *
     * @return array|null An associative array as below, and return NULL if the supplied oauth_token is invalid:
     * - client_id: Stored client identifier.
     * - expires: Stored expiration in unix timestamp.
     * - scope: (optional) Stored scope values in space-separated string.
     *
     * @ingroup oauth2_section_7
     */
    public function getAccessToken($oauthToken): ?array
    {
        $accessToken = $this->emn->getRepository('OAuth2ServerBundle:AccessToken')->find($oauthToken);

        if (!$accessToken) {
            return null;
        }

        $client = $accessToken->getClient();

        return array(
            'client_id' => $client->getClientId(),
            'user_id' => $accessToken->getUserId(),
            'expires' => $accessToken->getExpires()->getTimestamp(),
            'scope' => $accessToken->getScope()
        );
    }

    /**
     * Store the supplied access token values to storage.
     *
     * We need to store access token data as we create and verify tokens.
     *
     * @param string      $oauthToken oauth_token to be stored.
     * @param string      $clientId Client identifier to be stored.
     * @param string      $userId User identifier to be stored.
     * @param int         $expires Expiration to be stored as a Unix timestamp.
     * @param string|null $scope (optional) Scopes to be stored in space-separated string.
     *
     * @return null
     *
     * @throws ORMException
     *
     * @ingroup oauth2_section_4
     */
    public function setAccessToken($oauthToken, $clientId, $userId, $expires, $scope = null)
    {
        /**
         * @var Client $client
         */
        $client = $this->emn->getRepository('OAuth2ServerBundle:Client')->find($clientId);

        if (!$client) {
            return null;
        }

        // Create Access Token
        $accessToken = new EntityAccessToken();
        $accessToken->setToken($oauthToken);
        $accessToken->setClient($client);
        $accessToken->setUserId($userId);
        $accessToken->setExpires($expires);
        $accessToken->setScope($scope);

        // Store Access Token
        $this->emn->persist($accessToken);
        $this->emn->flush();
    }
}
