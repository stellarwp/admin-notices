<?php

declare(strict_types=1);


namespace StellarWP\AdminNotice\Actions;

use DateTimeImmutable;
use StellarWP\AdminNotice\AdminNotice;
use StellarWP\AdminNotice\AdminNotices;

class DisplayNoticesInAdmin
{
    public function __invoke()
    {
        $notices = AdminNotices::getNotices();

        if (empty($notices)) {
            return;
        }

        foreach ($notices as $notice) {
            if ($this->passesDateLimits($notice)
                && $this->passesWhenCallback($notice)
                && $this->passesUserCapabilities($notice)
                && $this->passesScreenConditions($notice)
            ) {
                echo (new RenderAdminNotice($notice))();
            }
        }
    }

    private function shouldDisplayNotice(AdminNotice $notice): bool {}

    private function passesDateLimits(AdminNotice $notice): bool
    {
        if (!$notice->getAfterDate() && !$notice->getUntilDate()) {
            return true;
        }

        $now = new DateTimeImmutable();

        if ($notice->getAfterDate() && $notice->getAfterDate() > $now) {
            return false;
        }

        if ($notice->getUntilDate() && $notice->getUntilDate() < $now) {
            return false;
        }

        return true;
    }

    private function passesWhenCallback(AdminNotice $notice): bool
    {
        $callback = $notice->getWhenCallback();

        if ($callback === null) {
            return true;
        }

        return $callback();
    }

    private function passesUserCapabilities(AdminNotice $notice): bool
    {
        $capabilities = $notice->getUserCapabilities();

        if (empty($capabilities)) {
            return true;
        }

        foreach ($capabilities as $capability) {
            if (is_string($capability)) {
                if (!current_user_can($capability)) {
                    return false;
                }
            } else {
                if (call_user_func_array('current_user_can', $capability) === false) {
                    return false;
                }
            }
        }

        return true;
    }

    private function passesScreenConditions(AdminNotice $notice): bool
    {
        $screen = get_current_screen();
        $currentUrl = get_admin_url(null, $_SERVER['REQUEST_URI']);

        $screenConditions = $notice->getOnConditions();

        if (empty($screenConditions)) {
            return true;
        }

        foreach ($screenConditions as $screenCondition) {
            $condition = $screenCondition->getCondition();

            if ($screenCondition->isRegex()) {
                // do a regex comparison on the current url
                if (!preg_match($condition, $currentUrl)) {
                    return false;
                }
            } elseif (is_string($condition)) {
                // do a string comparison on the current url
                if (!str_contains($currentUrl, $condition)) {
                    return false;
                }
            } else {
                // compare the condition array against the WP_Screen object
                foreach ($condition as $property => $value) {
                    if ($screen->$property !== $value) {
                        return false;
                    }
                }
            }
        }

        return true;
    }
}