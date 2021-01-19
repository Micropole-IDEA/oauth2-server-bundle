<?php

namespace OAuth2\ServerBundle\Storage;

use Symfony\Component\Security\Core\User\User;
use OAuth2\Storage\UserCredentialsInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use OAuth2\ServerBundle\User\OAuth2UserInterface;
use OAuth2\ServerBundle\User\AdvancedOAuth2UserInterface;

/**
 * Class UserCredentials
 */
class UserCredentials implements UserCredentialsInterface
{
    /**
     * @var EntityManager
     */
    private EntityManager $emn;

    /**
     * @var UserProviderInterface
     */
    private UserProviderInterface $upi;

    /**
     * @var EncoderFactoryInterface
     */
    private EncoderFactoryInterface $encoderFactory;

    /**
     * UserCredentials constructor.
     *
     * @param EntityManager           $entityManager
     * @param UserProviderInterface   $userProvider
     * @param EncoderFactoryInterface $encoderFactory
     */
    public function __construct(
        EntityManager $entityManager,
        UserProviderInterface $userProvider,
        EncoderFactoryInterface $encoderFactory
    ) {
        $this->emn = $entityManager;
        $this->upi = $userProvider;
        $this->encoderFactory = $encoderFactory;
    }

    /**
     * Grant access tokens for basic user credentials.
     *
     * Check the supplied username and password for validity.
     *
     * You can also use the $client_id param to do any checks required based
     * on a client, if you need that.
     *
     * Required for OAuth2::GRANT_TYPE_USER_CREDENTIALS.
     *
     * @param string $username Username to be check with.
     * @param string $password Password to be check with.
     *
     * @return bool TRUE if the username and password are valid, and FALSE if it isn't.
     * Moreover, if the username and password are valid, and you want to
     *
     * @see http://tools.ietf.org/html/rfc6749#section-4.3
     *
     * @ingroup oauth2_section_4
     */
    public function checkUserCredentials($username, $password): bool
    {
        try {
            $user = $this->upi->loadUserByUsername($username);
        } catch (UsernameNotFoundException $exception) {
            return false;
        }

        // Do extra checks if implementing the AdvancedUserInterface
        if ($user instanceof UserInterface) {
            /**
             * @var User $user
             */
            if (false === $user->isAccountNonExpired()) {
                return false;
            }
            if (false === $user->isAccountNonLocked()) {
                return false;
            }
            if (false === $user->isCredentialsNonExpired()) {
                return false;
            }
            if (false === $user->isEnabled()) {
                return false;
            }
        }

        // Check password
        if (
            $this->encoderFactory->getEncoder($user)->isPasswordValid(
                $user->getPassword(),
                $password,
                $user->getSalt()
            )
        ) {
            return true;
        }

        return false;
    }

    /**
     * getUserDetails
     *
     * @param string $username Username to be check with.
     *
     * @return string|bool|null|array
     * ARRAY the associated "user_id" and optional "scope" values
     * This function MUST return FALSE if the requested user does not exist or is
     * invalid. "scope" is a space-separated list of restricted scopes.
     * @code
     * return array(
     *     "user_id"  => USER_ID,    // REQUIRED user_id to be stored with the authorization code or access token
     *     "scope"    => SCOPE       // OPTIONAL space-separated list of restricted scopes
     * );
     * @endcode
     */
    public function getUserDetails($username)
    {
        // Load user by username
        try {
            $user = $this->upi->loadUserByUsername($username);
        } catch (UsernameNotFoundException $exception) {
            return false;
        }

        // If user implements OAuth2UserInterface or AdvancedOAuth2UserInterface
        // then we can get the scopes, score!
        $scope = null;
        if ($user instanceof OAuth2UserInterface || $user instanceof AdvancedOAuth2UserInterface) {
            $scope = $user->getScope();
        }

        return array(
            'user_id' => $user->getUsername(),
            'scope' => $scope
        );
    }
}
