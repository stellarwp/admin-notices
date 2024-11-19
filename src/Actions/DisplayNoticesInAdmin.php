<?php

declare(strict_types=1);


namespace StellarWP\AdminNotices\Actions;

use StellarWP\AdminNotices\AdminNotice;
use StellarWP\AdminNotices\Traits\HasNamespace;

/**
 * Displays the provided notices in the admin based on the conditions set in the notice.
 *
 * @since 1.1.0 added namespacing
 * @since 1.0.0
 */
class DisplayNoticesInAdmin
{
    use HasNamespace;

    /**
     * @since 1.1.0 passed the namespace to RenderAdminNotice
     * @since 1.0.0
     */
    public function __invoke(AdminNotice ...$notices)
    {
        if (empty($notices)) {
            return;
        }

        foreach ($notices as $notice) {
            if ((new NoticeShouldRender($this->namespace))($notice)) {
                echo (new RenderAdminNotice($this->namespace))($notice);
            }
        }
    }
}

