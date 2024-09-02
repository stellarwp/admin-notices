<?php

declare(strict_types=1);


namespace StellarWP\AdminNotices\ValueObjects;

/**
 * A simple VO to encapsulate a user capability and its parameters.
 *
 * @unreleased
 */
class UserCapability
{
    /**
     * @var string
     */
    private $capability;

    /**
     * @var array
     */
    private $parameters;

    /**
     * @unreleased
     */
    public function __construct(string $capability, array $parameters = [])
    {
        $this->capability = $capability;
        $this->parameters = $parameters;
    }

    /**
     * Checks of the current user passes the given capability.
     *
     * @unreleased
     */
    public function currentUserCan(): bool
    {
        return current_user_can($this->capability, ...$this->parameters);
    }
}
