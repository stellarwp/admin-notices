<?php

declare(strict_types=1);


namespace StellarWP\AdminNotices\ValueObjects;

use InvalidArgumentException;

class NoticeLocation
{
    /**
     * @var string
     */
    private $location;

    /**
     * @unreleased
     */
    public static function aboveHeader(): self
    {
        return new self('above_header');
    }

    /**
     * An alias of aboveHeader(), as this is the standard WordPress location for admin notices.
     *
     * @unreleased
     */
    public static function standard(): self
    {
        return self::belowHeader();
    }

    /**
     * @unreleased
     */
    public static function belowHeader(): self
    {
        return new self('below_header');
    }

    /**
     * @unreleased
     */
    public static function inline(): self
    {
        return new self('inline');
    }

    /**
     * @unreleased
     */
    public function __construct(string $location)
    {
        $this->validateLocation($location);

        $this->location = $location;
    }

    /**
     * @unreleased
     */
    public function __toString()
    {
        return $this->location;
    }

    /**
     * @unreleased
     */
    public function isAboveHeader(): bool
    {
        return $this->location === 'above_header';
    }

    /**
     * @unreleased
     */
    public function isBelowHeader(): bool
    {
        return $this->location === 'below_header';
    }

    /**
     * An alias of isAboveHeader(), as this is the standard WordPress location for admin notices.
     *
     * @unreleased
     */
    public function isStandard(): bool
    {
        return $this->isBelowHeader();
    }

    /**
     * @unreleased
     */
    public function isInline(): bool
    {
        return $this->location === 'inline';
    }

    /**
     * @unreleased
     *
     * @param string|self $location
     */
    public function equals($location): bool
    {
        if (!(is_string($location) || $location instanceof self)) {
            throw new InvalidArgumentException(
                'Invalid location: ' . gettype($location) . ' - must be a string or an instance of ' . self::class
            );
        }

        return $location instanceof self
            ? $this->location === $location->location
            : $this->location === $location;
    }

    /**
     * @unreleased
     */
    private function validateLocation(string $location)
    {
        $validLocations = [
            'above_header',
            'below_header',
            'inline',
        ];

        if (!in_array($location, $validLocations, true)) {
            throw new InvalidArgumentException(
                "Invalid location: $location - must be one of " . implode(', ', $validLocations)
            );
        }
    }
}
