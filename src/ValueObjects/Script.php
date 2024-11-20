<?php

declare(strict_types=1);


namespace StellarWP\AdminNotices\ValueObjects;

/**
 * Represents a script that can be enqueued with WordPress
 *
 * @since 2.0.0
 */
class Script
{
    /**
     * @var string
     */
    private $source;

    /**
     * @var string[]
     */
    private $dependencies;

    /**
     * @var string|bool|null
     */
    private $version;

    /**
     * @var array{strategy: string, inFooter: bool}
     */
    private $args;

    /**
     * @since 2.0.0
     */
    public function __construct(
        string $source,
        array $dependencies = [],
        $version = false,
        array $args = []
    ) {
        $this->source = $source;
        $this->dependencies = $dependencies;
        $this->version = $version;
        $this->args = $args;
    }

    /**
     * @since 2.0.0
     */
    public function enqueue(string $handle): void
    {
        wp_enqueue_script(
            $handle,
            $this->source,
            $this->dependencies,
            $this->version,
            $this->args
        );
    }
}
