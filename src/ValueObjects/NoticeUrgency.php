<?php

namespace StellarWP\AdminNotices\ValueObjects;

use InvalidArgumentException;

/**
 * A value object representing the urgency of a notice.
 *
 * @unreleased
 */
class NoticeUrgency
{
    const INFO = 'info';
    const WARNING = 'warning';
    const ERROR = 'error';
    const SUCCESS = 'success';

    /**
     * @var string
     */
    private $urgency;

    /**
     * @unreleased
     */
    public static function info(): self
    {
        return new self(self::INFO);
    }

    /**
     * @unreleased
     */
    public static function warning(): self
    {
        return new self(self::WARNING);
    }

    /**
     * @unreleased
     */
    public static function error(): self
    {
        return new self(self::ERROR);
    }

    /**
     * @unreleased
     */
    public static function success(): self
    {
        return new self(self::SUCCESS);
    }

    /**
     * @unreleased
     */
    public function __construct(string $urgency)
    {
        if (!in_array($urgency, [self::INFO, self::WARNING, self::ERROR, self::SUCCESS])) {
            throw new InvalidArgumentException('Invalid urgency');
        }

        $this->urgency = $urgency;
    }

    /**
     * @unreleased
     */
    public function __toString(): string
    {
        return $this->urgency;
    }
}
