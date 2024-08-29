<?php

declare(strict_types=1);


namespace StellarWP\AdminNotice\Actions;

use DateTimeImmutable;
use DateTimeZone;
use StellarWP\AdminNotice\AdminNotice;

/**
 * Displays the provided notices in the admin based on the conditions set in the notice.
 *
 * @unreleased
 */
class DisplayNoticesInAdmin
{
    /**
     * @unreleased
     */
    public function __invoke(AdminNotice ...$notices)
    {
        if (empty($notices)) {
            return;
        }

        foreach ($notices as $notice) {
            if ($this->shouldDisplayNotice($notice)) {
                echo (new RenderAdminNotice($notice))();
            }
        }
    }

    /**
     * Checks whether the notice should be displayed based on the provided conditions.
     *
     * @unreleased
     */
    private function shouldDisplayNotice(AdminNotice $notice): bool
    {
        return $this->passesDateLimits($notice)
               && $this->passesWhenCallback($notice)
               && $this->passesUserCapabilities($notice)
               && $this->passesScreenConditions($notice);
    }

    /**
     * Checks whether the notice should be displayed based on the provided date limits.
     *
     * @unreleased
     */
    private function passesDateLimits(AdminNotice $notice): bool
    {
        if (!$notice->getAfterDate() && !$notice->getUntilDate()) {
            return true;
        }

        $now = new DateTimeImmutable('now', new DateTimeZone('UTC'));

        if ($notice->getAfterDate() && $notice->getAfterDate() > $now) {
            return false;
        }

        if ($notice->getUntilDate() && $notice->getUntilDate() < $now) {
            return false;
        }

        return true;
    }

    /**
     * Checks whether the notice should be displayed based on the provided callback.
     *
     * @unreleased
     */
    private function passesWhenCallback(AdminNotice $notice): bool
    {
        $callback = $notice->getWhenCallback();

        if ($callback === null) {
            return true;
        }

        return $callback();
    }

    /**
     * Checks whether user limits were provided and they pass. Only one capability is required to pass, allowing for
     * multiple users have visibility.
     *
     * @unreleased
     */
    private function passesUserCapabilities(AdminNotice $notice): bool
    {
        $capabilities = $notice->getUserCapabilities();

        if (empty($capabilities)) {
            return true;
        }

        foreach ($capabilities as $capability) {
            if (is_string($capability)) {
                if (current_user_can($capability)) {
                    return true;
                }
            } else {
                if (current_user_can(...$capability)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Checks whether the notice is limited to specific screens and the current screen matches the conditions. Only one
     * screen condition is required to pass, allowing for the notice to appear on multiple screens.
     *
     * @unreleased
     */
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
                if (preg_match($condition, $currentUrl) === 1) {
                    return true;
                }
            } elseif (is_string($condition)) {
                // do a string comparison on the current url
                if (str_contains($currentUrl, $condition)) {
                    return true;
                }
            } else {
                // compare the condition array against the WP_Screen object
                foreach ($condition as $property => $value) {
                    if ($screen->$property === $value) {
                        return true;
                    }
                }
            }
        }

        return false;
    }
}
