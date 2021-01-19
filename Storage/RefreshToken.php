<?php

namespace OAuth2\ServerBundle\Storage;

use Doctrine\ORM\ORMException;
use OAuth2\ServerBundle\Entity\Client;
use OAuth2\Storage\RefreshTokenInterface;
use Doctrine\ORM\EntityManager;
use OAuth2\ServerBundle\Entity\RefreshToken as EntityRefreshToken;

/**
 * Class RefreshToken
 */
class RefreshToken implements RefreshTokenInterface
{
    /**
     * @var EntityManager
     */
    private EntityManager $emn;

    /**
     * RefreshToken constructor.
     *
     * @param EntityManager $EntityManager
     */
    public function __construct(EntityManager $EntityManager)
    {
        $this->emn = $EntityManager;
    }

    /**
     * Grant refresh access tokens.
     *
     * Retrieve the stored data for the given refresh token.
     *
     * Required for OAuth2::GRANT_TYPE_REFRESH_TOKEN.
     *
     * @param string $refreshToken Refresh token to be check with.
     *
     * @return array|null An associative array as below, and NULL if the refresh_token is invalid:
     * - refresh_token: Stored refresh token identifier.
     * - client_id: Stored client identifier.
     * - user_id: Stored user identifier.
     * - expires: Stored expiration unix timestamp.
     * - scope: (optional) Stored scope values in space-separated string.
     *
     * @see http://tools.ietf.org/html/rfc6749#section-6
     *
     * @ingroup oauth2_section_6
     */
    public function getRefreshToken($refreshToken): ?array
    {
        $refreshToken = $this->emn->getRepository('OAuth2ServerBundle:RefreshToken')->find($refreshToken);

        if (!$refreshToken) {
            return null;
        }

        // Get Client
        $client = $refreshToken->getClient();

        return array(
            'refresh_token' => $refreshToken->getToken(),
            'client_id' => $client->getClientId(),
            'user_id' => $refreshToken->getUserId(),
            'expires' => $refreshToken->getExpires()->getTimestamp(),
            'scope' => $refreshToken->getScope()
        );
    }

    /**
     * Take the provided refresh token values and store them somewhere.
     *
     * This function should be the storage counterpart to getRefreshToken().
     *
     * If storage fails for some reason, we're not currently checking for
     * any sort of success/failure, so you should bail out of the script
     * and provide a descriptive fail message.
     *
     * Required for OAuth2::GRANT_TYPE_REFRESH_TOKEN.
     *
     * @param string      $refreshToken Refresh token to be stored.
     * @param string      $clientId     Client identifier to be stored.
     * @param string      $userId       User identifier to be stored.
     * @param string      $expires      expires to be stored.
     * @param string|null $scope       (optional) Scopes to be stored in space-separated string.
     *
     * @return null
     *
     * @throws ORMException
     *
     * @ingroup oauth2_section_6
     */
    public function setRefreshToken($refreshToken, $clientId, $userId, $expires, $scope = null)
    {
        /**
         * @var Client $client
         */
        $client = $this->emn->getRepository('OAuth2ServerBundle:Client')->find($clientId);
        if (!$client) {
            return null;
        }

        // Create Refresh Token
        $refreshToken = new EntityRefreshToken();
        $refreshToken->setToken($refreshToken);
        $refreshToken->setClient($client);
        $refreshToken->setUserId($userId);
        $refreshToken->setExpires($expires);
        $refreshToken->setScope($scope);

        // Store Refresh Token
        $this->emn->persist($refreshToken);
        $this->emn->flush();
    }

    /**
     * Expire a used refresh token.
     *
     * This is not explicitly required in the spec, but is almost implied.
     * After granting a new refresh token, the old one is no longer useful and
     * so should be forcibly expired in the data store so it can't be used again.
     *
     * If storage fails for some reason, we're not currently checking for
     * any sort of success/failure, so you should bail out of the script
     * and provide a descriptive fail message.
     *
     * @param string $refreshToken Refresh token to be expirse.
     *
     * @throws ORMException
     *
     * @ingroup oauth2_section_6
     */
    public function unsetRefreshToken($refreshToken)
    {
        $refreshToken = $this->emn->getRepository('OAuth2ServerBundle:RefreshToken')->find($refreshToken);
        $this->emn->remove($refreshToken);
        $this->emn->flush();
    }
}
