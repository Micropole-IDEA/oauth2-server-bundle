<?php

namespace OAuth2\ServerBundle\Storage;

use OAuth2\Storage\ClientCredentialsInterface;
use Doctrine\ORM\EntityManager;

/**
 * Class ClientCredentials
 */
class ClientCredentials implements ClientCredentialsInterface
{
    /**
     * @var EntityManager
     */
    private EntityManager $emn;

    /**
     * ClientCredentials constructor.
     *
     * @param EntityManager $EntityManager
     */
    public function __construct(EntityManager $EntityManager)
    {
        $this->emn = $EntityManager;
    }

    /**
     * Make sure that the client credentials is valid.
     *
     * @param string      $clientId      Client identifier to be check with.
     * @param string|null $clientSecret  (optional) If a secret is required, check that they've given the right one.
     *
     * @return bool TRUE if the client credentials are valid, and MUST return FALSE if it isn't.
     *
     * @endcode
     *
     * @see http://tools.ietf.org/html/rfc6749#section-3.1
     *
     * @ingroup oauth2_section_3
     */
    public function checkClientCredentials($clientId, $clientSecret = null): bool
    {
        // Get Client
        $client = $this->emn->getRepository('OAuth2ServerBundle:Client')->find($clientId);

        // If client exists check secret
        if ($client) {
            return $client->getClientSecret() === $clientSecret;
        }

        return false;
    }

    /**
     * Get client details corresponding client_id.
     *
     * OAuth says we should store request URIs for each registered client.
     * Implement this function to grab the stored URI for a given client id.
     *
     * @param string $clientId Client identifier to be check with.
     *
     * @return array|bool
     *               Client details. The only mandatory key in the array is "redirect_uri".
     *               This function MUST return FALSE if the given client does not exist or is
     *               invalid. "redirect_uri" can be space-delimited to allow for multiple valid uris.
     * @code
     *               return array(
     *               "redirect_uri" => REDIRECT_URI,      // REQUIRED redirect_uri registered for the client
     *               "client_id"    => CLIENT_ID,         // OPTIONAL the client id
     *               "grant_types"  => GRANT_TYPES,       // OPTIONAL an array of restricted grant types
     *               );
     * @endcode
     *
     * @ingroup oauth2_section_4
     */
    public function getClientDetails($clientId)
    {
        $client = $this->emn->getRepository('OAuth2ServerBundle:Client')->find($clientId);

        if (!$client) {
            return false;
        }

        return array(
            'redirect_uri' => implode(' ', $client->getRedirectUri()),
            'client_id' => $client->getClientId(),
            'grant_types' => $client->getGrantTypes()
        );
    }

    /**
     * Check restricted grant types of corresponding client identifier.
     *
     * If you want to restrict clients to certain grant types, override this
     * function.
     *
     * @param string $clientId  Client identifier to be check with.
     * @param string $grantType Grant type to be check with
     *
     * @return bool TRUE if the grant type is supported by this client identifier, and FALSE if it isn't.
     *
     * @ingroup oauth2_section_4
     */
    public function checkRestrictedGrantType($clientId, $grantType): bool
    {
        $client = $this->getClientDetails($clientId);

        if (!$client) {
            return false;
        }

        if (empty($client['grant_types'])) {
            return true;
        }

        if (in_array($grantType, $client['grant_types'])) {
            return true;
        }

        return false;
    }

    /**
     * Determine if the client is a "public" client, and therefore
     * does not require passing credentials for certain grant types
     *
     * @param string $clientId Client identifier to be check with.
     *
     * @return bool TRUE if the client is public, and FALSE if it isn't.
     *
     * @endcode
     *
     * @see http://tools.ietf.org/html/rfc6749#section-2.3
     * @see https://github.com/bshaffer/oauth2-server-php/issues/257
     *
     * @ingroup oauth2_section_2
     */
    public function isPublicClient($clientId): bool
    {
        $client = $this->emn->getRepository('OAuth2ServerBundle:Client')->find($clientId);

        if (!$client) {
            return false;
        }

        $secret = $client->getClientSecret();

        return empty($secret);
    }

    /**
     * Get the scope associated with this client
     *
     * @param string $clientId Client identifier to be check with.
     *
     * @return string|bool STRING the space-delineated scope list for the specified client_id
     */
    public function getClientScope($clientId)
    {
        $client = $this->emn->getRepository('OAuth2ServerBundle:Client')->find($clientId);

        if (!$client) {
            return false;
        }

        return implode(' ', $client->getScopes());
    }
}
