<?php

declare(strict_types=1);


namespace StellarWP\AdminNotices;

use StellarWP\AdminNotices\Contracts\NotificationsRegistrarInterface;
use StellarWP\AdminNotices\Exceptions\NotificationCollisionException;

class NotificationsRegistrar implements NotificationsRegistrarInterface
{
    protected $notices = [];

    /**
     * {@inheritDoc}
     *
     * @unreleased
     */
    public function registerNotice(string $id, AdminNotice $notice): void
    {
        if (isset($this->notices[$id])) {
            throw new NotificationCollisionException($id, $notice);
        }

        $this->notices[$id] = $notice;
    }

    /**
     * {@inheritDoc}
     *
     * @unreleased
     */
    public function unregisterNotice(string $id): void
    {
        unset($this->notices[$id]);
    }

    /**
     * {@inheritDoc}
     *
     * @unreleased
     */
    public function getNotices(): array
    {
        return array_values($this->notices);
    }
}
