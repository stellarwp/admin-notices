<?php

declare(strict_types=1);


namespace StellarWP\AdminNotices\Actions;

use StellarWP\AdminNotices\AdminNotice;
use StellarWP\AdminNotices\DataTransferObjects\NoticeElementProperties;
use StellarWP\AdminNotices\Traits\HasNamespace;
use WP_HTML_Tag_Processor;

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
     * Renders the admin notice
     *
     * @since 2.0.0 custom notices have a completely different render flow
     * @since 1.1.0 added namespacing and notice is passed to the __invoke method
     * @since 1.0.0
     */
    public function __invoke(AdminNotice $notice): string
    {
        $elementProperties = new NoticeElementProperties($notice, $this->namespace);
        $renderTextOrCallback = $notice->getRenderTextOrCallback();

        if (is_callable($renderTextOrCallback)) {
            $content = $renderTextOrCallback($notice, $elementProperties);
        } else {
            $content = $renderTextOrCallback;
        }

        if ($notice->isCustom()) {
            return $this->applyCustomAttributesToContent($content, $notice);
        }

        return sprintf(
            "<div class='%s' $elementProperties->idAttribute>%s</div>",
            esc_attr($this->getStandardWrapperClasses($notice)),
            $notice->shouldAutoParagraph() ? wpautop($content) : $content
        );
    }

    /**
     * Generates the classes for the standard WordPress notice wrapper.
     *
     * @since 2.0.0 notice is assumed to be standard only
     * @since 1.1.0 notice is passed instead of accessed as a property
     * @since 1.0.0
     */
    private function getStandardWrapperClasses(AdminNotice $notice): string
    {
        $classes = [
            'notice',
            "notice-{$notice->getUrgency()}",
        ];

        if ($notice->isDismissible()) {
            $classes[] = "is-dismissible";
        }

        if ($notice->getLocation() && $notice->getLocation()->isInline()) {
            $classes[] = 'inline';
        }

        if ($notice->usesAlternateStyles()) {
            $classes[] = 'notice-alt';
        }

        return implode(' ', $classes);
    }

    /**
     * Apply the needed custom attributes to the content.
     *
     * @since 2.0.0
     */
    private function applyCustomAttributesToContent(
        string $content,
        AdminNotice $notice
    ): string {
        $tags = new WP_HTML_Tag_Processor($content);

        $tags->next_tag();

        $tags->set_attribute("data-stellarwp-{$this->namespace}-notice-id", $notice->getId());

        if ($notice->getLocation()) {
            $tags->set_attribute("data-stellarwp-{$this->namespace}-location", (string)$notice->getLocation());
        }

        return $tags->__toString();
    }
}
