<?php

declare(strict_types=1);


namespace StellarWP\AdminNotice\Exceptions;

use RuntimeException;
use StellarWP\AdminNotice\AdminNotice;

class NotificationCollisionException extends RuntimeException
{
    protected $notificationId;

    protected $notification;

    public function __construct(string $notificationId, AdminNotice $notification)
    {
        $this->notificationId = $notificationId;
        $this->notification = $notification;

        parent::__construct("Notification with ID $notificationId already exists.");
    }
}