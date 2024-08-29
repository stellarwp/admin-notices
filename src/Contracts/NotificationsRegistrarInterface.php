<?php

declare(strict_types=1);

namespace StellarWP\AdminNotices\Contracts;

use StellarWP\AdminNotices\AdminNotice;
use StellarWP\AdminNotices\Exceptions\NotificationCollisionException;

interface NotificationsRegistrarInterface
{
    /**
     * Adds a notice to the register and throws a NotificationCollisionException if a notice with the same ID already exists.
     *
     * @unreleased
     *
     * @throws NotificationCollisionException
     */
    public function registerNotice(string $id, AdminNotice $notice): void;

    /**
     * Removes a notice from the register.
     *
     * @unreleased
     */
    public function unregisterNotice(string $id): void;

    /**
     * Returns all the notices in the register.
     *
     * @unreleased
     *
     * @return AdminNotice[]
     */
    public function getNotices(): array;
}
