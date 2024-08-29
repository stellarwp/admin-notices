<?php

declare(strict_types=1);


namespace StellarWP\AdminNotices\Actions;

use StellarWP\AdminNotices\AdminNotice;

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
        if (!$this->notice->usesWrapper()) {
            return $this->notice->getRenderedContent();
        }

        return sprintf(
            "<div class='%s'>%s</div>",
            esc_attr($this->getWrapperClasses()),
            $this->notice->getRenderedContent()
        );
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
