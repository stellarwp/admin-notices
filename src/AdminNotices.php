<?php

declare(strict_types=1);

namespace StellarWP\AdminNotices;

use Psr\Container\ContainerInterface;
use RuntimeException;
use StellarWP\AdminNotices\Actions\DisplayNoticesInAdmin;
use StellarWP\AdminNotices\Contracts\NotificationsRegistrarInterface;

class AdminNotices
{
    /**
     * @var ContainerInterface
     */
    protected static $container;

    /**
     * @var NotificationsRegistrarInterface
     */
    protected static $registrar;

    /**
     * @var string used in actions, filters, and data storage
     */
    protected static $prefix = '';

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

        self::getRegistrar()->registerNotice($notificationId, $notice);

        return $notice;
    }

    /**
     * Removes a registered notice so it will no longer be shown
     *
     * @unreleased
     */
    public static function removeNotice(string $notificationId): void
    {
        self::getRegistrar()->unregisterNotice($notificationId);
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
     * Initializes the package. Required to be called to display the notices.
     *
     * This should be called at the beginning of the plugin file along with other configuration.
     *
     * @unreleased
     */
    public function initialize(): void
    {
        add_action('admin_notices', [self::class, 'setUpNotices']);
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
        return self::getRegistrar()->getNotices();
    }

    /**
     * Hook action to display the notices in the admin
     *
     * @unreleased
     */
    public static function setUpNotices(): void
    {
        (new DisplayNoticesInAdmin())(...self::getNotices());
    }

    /**
     * Returns the registrar instance, from the container if available, otherwise a locally stored instance
     *
     * @unreleased
     */
    private static function getRegistrar(): NotificationsRegistrarInterface
    {
        if (self::$registrar !== null) {
            return self::$registrar;
        }

        if (self::$container && !self::$container->has(NotificationsRegistrarInterface::class)) {
            throw new RuntimeException('NotificationsRegistrarInterface not found in container');
        }

        if (self::$container) {
            self::$registrar = self::$container->get(NotificationsRegistrarInterface::class);
        } else {
            self::$registrar = new NotificationsRegistrar();
        }

        return self::$registrar;
    }
}
