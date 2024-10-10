<?php

declare(strict_types=1);

use StellarWP\AdminNotices\AdminNotice;
use StellarWP\AdminNotices\NotificationsRegistrar;
use StellarWP\AdminNotices\Tests\Support\Helper\TestCase;

/**
 * @coversDefaultClass \StellarWP\AdminNotices\NotificationsRegistrar
 */
class NotificationsRegistrarTest extends TestCase
{
    /**
     * @covers ::registerNotice
     * @covers ::getNotices
     *
     * @unreleased
     */
    public function testRegisterNotice(): void
    {
        $id = 'test';
        $notice = new AdminNotice('test_id', 'test');
        $registrar = new NotificationsRegistrar();
        $registrar->registerNotice($notice);
        $this->assertSame($notice, $registrar->getNotices()[0]);
    }

    /**
     * @covers ::unregisterNotice
     *
     * @unreleased
     */
    public function testUnregisterNotice(): void
    {
        $registrar = new NotificationsRegistrar();

        $idToKeep = 'test-keep';
        $noticeToKeep = new AdminNotice($idToKeep, 'test');

        $idToRemove = 'test-remove';
        $noticeToRemove = new AdminNotice($idToRemove, 'test-remove');

        $registrar->registerNotice($noticeToKeep);
        $registrar->registerNotice($noticeToRemove);
        $registrar->unregisterNotice($idToRemove);

        $this->assertSame([$noticeToKeep], $registrar->getNotices());
    }
}
