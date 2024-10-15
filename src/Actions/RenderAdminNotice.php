<?php

declare(strict_types=1);


namespace StellarWP\AdminNotices\Actions;

use StellarWP\AdminNotices\AdminNotice;
use StellarWP\AdminNotices\Traits\HasNamespace;

/**
 * Renders the admin notice based on the configuration of the notice.
 *
 * @since 1.1.0 refactored to use namespace and notice is passed to the __invoke method
 * @since 1.0.0
 */
class RenderAdminNotice
{
    use HasNamespace;

    /**
     * @var AdminNotice
     */
    private $notice;

    /**
     * Renders the admin notice
     *
     * @since 1.1.0 added namespacing and notice is passed to the __invoke method
     * @since 1.0.0
     */
    public function __invoke(AdminNotice $notice): string
    {
        if (!$notice->usesWrapper()) {
            return $notice->getRenderedContent();
        }

        return sprintf(
            "<div class='%s' data-stellarwp-$this->namespace-notice-id='%s'>%s</div>",
            esc_attr($this->getWrapperClasses($notice)),
            $notice->getId(),
            $notice->getRenderedContent()
        );
    }

    /**
     * Generates the classes for the standard WordPress notice wrapper.
     *
     * @since 1.1.0 notice is passed instead of accessed as a property
     * @since 1.0.0
     */
    private function getWrapperClasses(AdminNotice $notice): string
    {
        $classes = ['notice', 'notice-' . $notice->getUrgency()];

        if ($notice->isDismissible()) {
            $classes[] = "is-dismissible";
        }

        return implode(' ', $classes);
    }
}
