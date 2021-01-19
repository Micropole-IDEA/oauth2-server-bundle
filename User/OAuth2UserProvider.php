<?php

namespace OAuth2\ServerBundle\User;

use Doctrine\ORM\ORMException;
use OAuth2\ServerBundle\Entity\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

/**
 * Class OAuth2UserProvider
 */
class OAuth2UserProvider implements UserProviderInterface
{
    /**
     * @var EntityManager
     */
    private EntityManager $emn;

    /**
     * @var EncoderFactoryInterface
     */
    private EncoderFactoryInterface $encoderFactory;

    /**
     * OAuth2UserProvider constructor.
     *
     * @param EntityManager           $entityManager
     * @param EncoderFactoryInterface $encoderFactory
     */
    public function __construct(EntityManager $entityManager, EncoderFactoryInterface $encoderFactory)
    {
        $this->emn = $entityManager;
        $this->encoderFactory = $encoderFactory;
    }

    /**
     * Loads the user for the given username.
     *
     * This method must throw UsernameNotFoundException if the user is not
     * found.
     *
     * @param string $username The username
     *
     * @return UserInterface
     *
     * @throws UsernameNotFoundException if the user is not found
     * @see UsernameNotFoundException
     *
     */
    public function loadUserByUsername($username): UserInterface
    {
        /**
         * @var UserInterface $user
         */
        $user = $this->emn->getRepository('OAuth2ServerBundle:User')->find($username);

        if (!$user) {
            throw new UsernameNotFoundException(sprintf('Username "%s" not found.', $username));
        }

        return $user;
    }

    /**
     * Refreshes the user for the account interface.
     *
     * It is up to the implementation to decide if the user data should be
     * totally reloaded (e.g. from the database), or if the UserInterface
     * object can just be merged into some internal array of users / identity
     * map.
     * @param UserInterface $user
     *
     * @return UserInterface
     *
     * @throws UnsupportedUserException if the account is not supported
     */
    public function refreshUser(UserInterface $user): UserInterface
    {
        if (!$user instanceof OAuth2UserInterface) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    /**
     * Whether this provider supports the given user class
     *
     * @param string $class
     *
     * @return bool
     */
    public function supportsClass($class): bool
    {
        if ('OAuth2UserInterface' === $class) {
            return true;
        }

        return false;
    }

    /**
     * Creates a new user
     *
     * @param string $username
     * @param string $password
     * @param array  $roles
     * @param array  $scopes
     *
     * @return UserInterface
     *
     * @throws ORMException
     */
    public function createUser($username, $password, array $roles = array(), array $scopes = array()): UserInterface
    {
        $user = new User();
        $user->setUsername($username);
        $user->setRoles($roles);
        $user->setScopes($scopes);

        // Generate password
        $salt = $this->generateSalt();
        $password = $this->encoderFactory->getEncoder($user)->encodePassword($password, $salt);

        $user->setSalt($salt);
        $user->setPassword($password);

        // Store User
        $this->emn->persist($user);
        $this->emn->flush();

        return $user;
    }

    /**
     * Creates a salt for password hashing
     *
     * @return string A salt
     */
    protected function generateSalt(): string
    {
        return base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
    }
}
