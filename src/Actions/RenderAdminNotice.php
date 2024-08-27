<?php

declare(strict_types=1);


namespace StellarWP\AdminNotice\Actions;

use StellarWP\AdminNotice\AdminNotice;

/**
 * Renders the admin notice based on the configuration of the notice.
 *
 * @unreleased
 */
class RenderAdminNotice
{
    /**
     * @var AdminNotice
     */
    private $notice;

    public function __construct(AdminNotice $notice)
    {
        $this->notice = $notice;
    }

    public function __invoke(): string
    {
        if (!$this->notice->usesContainer()) {
            return $this->notice->getRenderedContent();
        }

        return "<div class='{$this->getWrapperClasses()}'>{$this->notice->getRenderedContent()}</div>";
    }

    /**
     * Generates the classes for the standard WordPress notice wrapper.
     *
     * @unreleased
     */
    private function getWrapperClasses(): string
    {
        $classes = ['notice', 'notice-' . $this->notice->getUrgency()];

        if ($this->notice->isDismissible()) {
            $classes[] = 'is-dismissible';
        }

        return implode(' ', $classes);
    }
}