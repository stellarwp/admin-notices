<?php

declare(strict_types=1);

use StellarWP\AdminNotices\AdminNotice;
use StellarWP\AdminNotices\AdminNotices;
use StellarWP\AdminNotices\Contracts\NotificationsRegistrarInterface;
use StellarWP\AdminNotices\Tests\Support\Helper\TestCase;
use StellarWP\AdminNotices\Tests\Support\Traits\WithUopz;

/**
 * @coversDefaultClass \StellarWP\AdminNotices\AdminNotices
 */
class AdminNoticesTest extends TestCase
{
    use WithUopz;

    /**
     * @covers ::show
     *
     * @since 1.0.0
     */
    public function testShouldRegisterAdminNotice()
    {
        $mockRegistrar = $this->createMock(NotificationsRegistrarInterface::class);
        $mockRegistrar
            ->expects($this->once())
            ->method('registerNotice')
            ->with($this->isInstanceOf(StellarWP\AdminNotices\AdminNotice::class));

        $this->setUpContainerWithMockRegistrar($mockRegistrar);

        $notice = AdminNotices::show('test', 'This is a test message.');
        $this->assertInstanceOf(StellarWP\AdminNotices\AdminNotice::class, $notice);
        $this->assertEquals('This is a test message.', $notice->getRenderTextOrCallback());
        $this->assertSame('/test', $notice->getId());
    }

    /**
     * @covers ::render
     *
     * @since 1.0.0
     */
    public function testShouldRenderAdminNotice()
    {
        $notice = new AdminNotice('test', 'This is a test message.');

        AdminNotices::render($notice);

        $this->expectOutputString(
            '<div class=\'notice notice-info\' data-notice-id=\'test\'>This is a test message.</div>'
        );
    }

    /**
     * @covers ::render
     *
     * @since 1.0.0
     */
    public function testShouldReturnAdminNoticeHtml()
    {
        $notice = new AdminNotice('test', 'This is a test message.');

        $html = AdminNotices::render($notice, false);

        $this->assertEquals(
            '<div class=\'notice notice-info\' data-notice-id=\'test\'>This is a test message.</div>',
            $html
        );
    }

    /**
     * @covers ::removeNotice
     *
     * @since 1.0.0
     */
    public function testShouldRemoveNotice()
    {
        $mockRegistrar = $this->createMock(NotificationsRegistrarInterface::class);
        $mockRegistrar
            ->expects($this->once())
            ->method('unregisterNotice')
            ->with('test');

        $this->setUpContainerWithMockRegistrar($mockRegistrar);

        AdminNotices::removeNotice('test');
    }

    /**
     * @covers ::initialize
     *
     * @since 1.0.0
     */
    public function testInitialize(): void
    {
        AdminNotices::initialize('test', 'https://example.com');

        $this->assertSame(10, has_action('admin_notices', [AdminNotices::class, 'setUpNotices']));
        $this->assertSame(10, has_action('admin_enqueue_scripts', [AdminNotices::class, 'enqueueScripts']));
    }

    /**
     * @covers ::initialize
     *
     * @since 1.0.0
     */
    public function testShouldThrowExceptionOnEmptyNamespace()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Namespace must be provided');

        AdminNotices::initialize('', 'https://example.com');
    }

    /**
     * Adds a container to the AdminNotices class with a mock registrar
     *
     * @since 1.0.0
     */
    private function setUpContainerWithMockRegistrar($mockRegistrar): void
    {
        AdminNotices::setContainer(new ServiceContainer($mockRegistrar));
    }
}

/**
 * A simple service container for testing
 *
 * @since 1.0.0
 */
class ServiceContainer implements Psr\Container\ContainerInterface
{
    private $mockRegistrar;

    public function __construct($mockRegistrar)
    {
        $this->mockRegistrar = $mockRegistrar;
    }

    public function get(string $id)
    {
        if ($id === NotificationsRegistrarInterface::class) {
            return $this->mockRegistrar;
        }

        throw new RuntimeException('Service not found');
    }

    public function has(string $id): bool
    {
        if ($id === NotificationsRegistrarInterface::class) {
            return true;
        }

        throw new RuntimeException('Service not found');
    }
}
