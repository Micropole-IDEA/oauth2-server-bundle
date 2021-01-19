<?php

namespace OAuth2\ServerBundle\Storage\OpenID;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Exception;
use OAuth2\OpenID\Storage\AuthorizationCodeInterface;

class AuthorizationCode implements AuthorizationCodeInterface
{
    /**
     * @var EntityManager
     */
    private EntityManager $emn;

    /**
     * AuthorizationCode constructor.
     * 
     * @param EntityManager $EntityManager
     */
    public function __construct(EntityManager $EntityManager)
    {
        $this->emn = $EntityManager;
    }

    /**
     * Fetch authorization code data (probably the most common grant type).
     * Retrieve the stored data for the given authorization code.
     * Required for OAuth2::GRANT_TYPE_AUTH_CODE.
     *
     * @param string $code Authorization code to be check with.
     *
     * @return array|null An associative array as below, and NULL if the code is invalid
     *
     * @code
     * return array(
     *     "clientId"    => clientId,      // REQUIRED Stored client identifier
     *     "userId"      => userId,        // REQUIRED Stored user identifier
     *     "expires"      => EXPIRES,        // REQUIRED Stored expiration in unix timestamp
     *     "redirectUri" => redirectUri,   // REQUIRED Stored redirect URI
     *     "scope"        => SCOPE,          // OPTIONAL Stored scope values in space-separated string
     *     "open_id"      => OPEN_ID,        // OPTIONAL Stored ID Token
     * );
     * @endcode
     *
     * @see http://tools.ietf.org/html/rfc6749#section-4.1
     *
     * @ingroup oauth2_section_4
     */
    public function getAuthorizationCode($code): ?array
    {
        // Get Code
        $code = $this->emn->getRepository('OAuth2ServerBundle:AuthorizationCode')->find($code);

        if (!$code) {
            return null;
        }

        return array(
            'clientId' => $code->getClient()->getClientId(),
            'userId' => $code->getUserId(),
            'expires' => $code->getExpires()->getTimestamp(),
            'redirectUri' => implode(' ', $code->getRedirectUri()),
            'scope' => $code->getScope(),
            'idToken' => $code->getIdToken(),
        );
    }

    /**
     * Take the provided authorization code values and store them somewhere.
     *
     * This function should be the storage counterpart to getAuthCode().
     *
     * If storage fails for some reason, we're not currently checking for
     * any sort of success/failure, so you should bail out of the script
     * and provide a descriptive fail message.
     *
     * Required for OAuth2::GRANT_TYPE_AUTH_CODE.
     *
     * @param string      $code        Authorization code to be stored.
     * @param string      $clientId    Client identifier to be stored.
     * @param string      $userId      User identifier to be stored.
     * @param string      $redirectUri Redirect URI(s) to be stored in a space-separated string.
     * @param int         $expires     Expiration to be stored as a Unix timestamp.
     * @param string|null $scope       (optional) Scopes to be stored in space-separated string.
     * @param string|null $idToken     (optional) OpenID Token
     *
     * @ingroup oauth2_section_4
     *
     * @throws Exception
     */
    public function setAuthorizationCode($code, $clientId, $userId, $redirectUri, $expires, $scope = null, $idToken = null)
    {
        $client = $this->emn->getRepository('OAuth2ServerBundle:Client')->find($clientId);

        if (!$client) throw new Exception('Unknown client identifier');

        if (!$authorizationCode = $this->getAuthorizationCode($code)) {
          $authorizationCode = new \OAuth2\ServerBundle\Entity\AuthorizationCode();
        }

        $authorizationCode->setCode($code);
        $authorizationCode->setClient($client);
        $authorizationCode->setUserId($userId);
        $authorizationCode->setRedirectUri($redirectUri);
        $authorizationCode->setExpires($expires);
        $authorizationCode->setScope($scope);

        if ($idToken) {
          $authorizationCode->setIdToken($idToken);
        }

        $this->emn->persist($authorizationCode);
        $this->emn->flush();
    }

    /**
     * once an Authorization Code is used, it must be exipired
     *
     * @see http://tools.ietf.org/html/rfc6749#section-4.1.2
     *
     *    The client MUST NOT use the authorization code
     *    more than once.  If an authorization code is used more than
     *    once, the authorization server MUST deny the request and SHOULD
     *    revoke (when possible) all tokens previously issued based on
     *    that authorization code
     * @param string $code
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function expireAuthorizationCode($code)
    {
        $code = $this->emn->getRepository('OAuth2ServerBundle:AuthorizationCode')->find($code);
        $this->emn->remove($code);
        $this->emn->flush();
    }
}
