<?php

declare(strict_types=1);


namespace StellarWP\AdminNotices\ValueObjects;

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
