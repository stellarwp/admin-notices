<?php

declare(strict_types=1);

namespace StellarWP\AdminNotice;

use Psr\Container\ContainerInterface;
use RuntimeException;
use StellarWP\AdminNotice\Contracts\NotificationsRegisterInterface;

class AdminNotices
{
    /**
     * @var ContainerInterface
     */
    protected static $container;

    /**
     * Registers a notice to be conditionally displayed in the admin
     *
     * @unreleased
     *
     * @param string|callable $render
     */
    public static function show(string $notificationId, $render): AdminNotice
    {
        $notice = new AdminNotice($render);

        self::getRegister()->registerNotice($notificationId, $notice);

        return $notice;
    }

    /**
     * Removes a registered notice so it will no longer be shown
     *
     * @unreleased
     */
    public static function removeNotice(string $notificationId): void
    {
        self::getRegister()->unregisterNotice($notificationId);
    }

    /**
     * Sets the container with the register stored to be used for storing notices
     *
     * @unreleased
     */
    public static function setContainer(ContainerInterface $container): void
    {
        self::$container = $container;
    }

    /**
     * Returns the notices stored in the register
     *
     * @unreleased
     *
     * @return AdminNotice[]
     */
    public static function getNotices(): array
    {
        return self::getRegister()->getNotices();
    }

    /**
     * Returns the register instance, from the container if available, otherwise a locally stored instance
     *
     * @unreleased
     */
    private static function getRegister(): NotificationsRegisterInterface
    {
        static $register = null;

        if ($register !== null) {
            return $register;
        }

        if (self::$container && !self::$container->has(NotificationsRegisterInterface::class)) {
            throw new RuntimeException('NotificationsRegisterInterface not found in container');
        }

        if (self::$container) {
            $register = self::$container->get(NotificationsRegisterInterface::class);
        } else {
            $register = new NotificationsRegister();
        }

        return $register;
    }
}