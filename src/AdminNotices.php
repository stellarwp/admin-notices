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
    protected static $namespace = '';

    /**
     * @var string the URL to the package, used for enqueuing scripts
     */
    protected static $packageUrl;

    /**
     * Registers a notice to be conditionally displayed in the admin
     *
     * @since 1.0.0
     *
     * @param string|callable $render
     */
    public static function show(string $notificationId, $render): AdminNotice
    {
        $notice = new AdminNotice(self::$namespace . '/' . $notificationId, $render);

        self::getRegistrar()->registerNotice($notice);

        return $notice;
    }

    /**
     * Immediately renders a notice, useful when wanting to display a notice in an ad hoc context
     *
     * @since 1.0.0
     *
     * @param bool $echo whether to echo or return the notice
     *
     * @return string|null
     */
    public static function render(AdminNotice $notice, bool $echo = true): ?string
    {
        ob_start();
        (new DisplayNoticesInAdmin())($notice);
        $output = ob_get_clean();

        if ($echo) {
            echo $output;

            return null;
        } else {
            return $output;
        }
    }

    /**
     * Removes a registered notice so it will no longer be shown
     *
     * @since 1.0.0
     */
    public static function removeNotice(string $notificationId): void
    {
        self::getRegistrar()->unregisterNotice($notificationId);
    }

    /**
     * Sets the container with the register stored to be used for storing notices
     *
     * @since 1.0.0
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
     * @since 1.0.0
     */
    public static function initialize(string $namespace, string $pluginUrl): void
    {
        self::$packageUrl = $pluginUrl;
        self::$namespace = $namespace;

        add_action('admin_notices', [self::class, 'setUpNotices']);
        add_action('admin_enqueue_scripts', [self::class, 'enqueueScripts']);
    }

    /**
     * Returns the notices stored in the register
     *
     * @since 1.0.0
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
     * @since 1.0.0
     */
    public static function setUpNotices(): void
    {
        (new DisplayNoticesInAdmin())(...self::getNotices());
    }

    public static function enqueueScripts(): void
    {
        // use the version from the composer.json file
        $composerJson = json_decode(file_get_contents(__DIR__ . '/../composer.json'), true);
        $version = $composerJson['version'];

        wp_enqueue_script(
            'stellarwp-admin-notices',
            self::$packageUrl . '/src/resources/admin-notices.js',
            ['jquery', 'wp-data', 'wp-preferences'],
            $version,
            true
        );
    }

    /**
     * Returns the registrar instance, from the container if available, otherwise a locally stored instance
     *
     * @since 1.0.0
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
