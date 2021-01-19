<?php

namespace OAuth2\ServerBundle\Exception;

use Exception;

/**
 * Class ScopeNotFoundException
 */
class ScopeNotFoundException extends Exception
{
    /**
     * {@inheritDoc}
     */
    public function getMessageKey(): string
    {
        return 'Scope could not be found.';
    }

    /**
     * {@inheritDoc}
     */
    public function serialize(): string
    {
        return serialize(array(
            parent::serialize(),
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function unserialize(string $str)
    {
        list($parentData) = unserialize($str);

        parent::unserialize($parentData);
    }
}
