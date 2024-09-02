<?php

declare(strict_types=1);

use StellarWP\AdminNotices\AdminNotice;
use StellarWP\AdminNotices\NotificationsRegistrar;
use StellarWP\AdminNotices\Tests\TestCase;

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
        $notice = new AdminNotice('test');
        $registrar = new NotificationsRegistrar();
        $registrar->registerNotice($id, $notice);
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

        $idToKeep = 'test';
        $noticeToKeep = new AdminNotice('test');

        $idToRemove = 'test-remove';
        $noticeToRemove = new AdminNotice('test-remove');

        $registrar->registerNotice($idToKeep, $noticeToKeep);
        $registrar->registerNotice($idToRemove, $noticeToRemove);
        $registrar->unregisterNotice($idToRemove);

        $this->assertSame([$noticeToKeep], $registrar->getNotices());
    }
}
