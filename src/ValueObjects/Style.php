<?php

declare(strict_types=1);


namespace StellarWP\AdminNotices\ValueObjects;

/**
 * Represents a stylesheet to be enqueued in WordPress
 *
 * @since 2.0.0
 */
class Style
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
     * @var string
     */
    private $media;

    /**
     * @since 2.0.0
     */
    public function __construct(
        string $source,
        array $dependencies = [],
        $version = false,
        string $media = 'all'
    ) {
        $this->source = $source;
        $this->dependencies = $dependencies;
        $this->version = $version;
        $this->media = $media;
    }

    /**
     * Enqueues the stylesheet with WordPress
     *
     * @since 2.0.0
     */
    public function enqueue(string $handle): void
    {
        wp_enqueue_style(
            $handle,
            $this->source,
            $this->dependencies,
            $this->version,
            $this->media
        );
    }
}
