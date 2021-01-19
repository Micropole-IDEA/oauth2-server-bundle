<?php

namespace OAuth2\ServerBundle\User;

use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Interface AdvancedOAuth2UserInterface
 */
interface AdvancedOAuth2UserInterface extends UserInterface
{
    /**
     * Returns the scope granted to the user,
     * space-separated.
     *
     * <code>
     * public function getScope()
     * {
     *     return 'basic email';
     * }
     * </code>
     *
     *
     * @return string The user scope
     */
    public function getScope(): string;
}
