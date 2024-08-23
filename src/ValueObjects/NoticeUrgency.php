<?php

namespace StellarWP\AdminNotice\ValueObjects;

use InvalidArgumentException;

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

    public static function info(): self
    {
        return new self(self::INFO);
    }

    public static function warning(): self
    {
        return new self(self::WARNING);
    }

    public static function error(): self
    {
        return new self(self::ERROR);
    }

    public static function success(): self
    {
        return new self(self::SUCCESS);
    }

    public function __construct(string $urgency)
    {
        if (!in_array($urgency, [self::INFO, self::WARNING, self::ERROR, self::SUCCESS])) {
            throw new InvalidArgumentException('Invalid urgency');
        }

        $this->urgency = $urgency;
    }

    public function __toString(): string
    {
        return $this->urgency;
    }
}