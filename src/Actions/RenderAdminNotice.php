<?php

declare(strict_types=1);


namespace StellarWP\AdminNotices\Actions;

use StellarWP\AdminNotices\AdminNotice;
use StellarWP\AdminNotices\DataTransferObjects\NoticeElementProperties;
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
     * Renders the admin notice
     *
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

        if (!$notice->isCustom() && $notice->shouldAutoParagraph()) {
            $content = wpautop($content);
        }

        if ($notice->isCustom()) {
            $locationAttribute = $notice->getLocation()
                ? $elementProperties->customLocationAttribute
                : '';

            return sprintf(
                "<div %s %s>%s</div>",
                $elementProperties->idAttribute,
                $locationAttribute,
                $content
            );
        }

        return sprintf(
            "<div class='%s' $elementProperties->idAttribute>%s</div>",
            esc_attr($this->getStandardWrapperClasses($notice)),
            $content
        );
    }

    /**
     * Generates the classes for the standard WordPress notice wrapper.
     *
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

        if ($notice->getLocation()->isInline()) {
            $classes[] = 'inline';
        }

        if ($notice->usesAlternateStyles()) {
            $classes[] = 'notice-alt';
        }

        return implode(' ', $classes);
    }

    private function getCustomWrapperClasses(AdminNotice $notice, NoticeElementProperties $elementProperties): string
    {
        $classes = [
            $elementProperties->customNoticeClass,
        ];

        if ($notice->getLocation()) {
            $classes[] = $elementProperties->customLocationClass;
        }

        return implode(' ', $classes);
    }
}
