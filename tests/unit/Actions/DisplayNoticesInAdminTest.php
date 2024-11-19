<?php

declare(strict_types=1);

use StellarWP\AdminNotices\Actions\DisplayNoticesInAdmin;
use StellarWP\AdminNotices\AdminNotice;
use StellarWP\AdminNotices\Tests\Support\Helper\TestCase;

/**
 * @coversDefaultClass \StellarWP\AdminNotices\Actions\DisplayNoticesInAdmin
 */
class DisplayNoticesInAdminTest extends TestCase
{
    /**
     * @var array $originalServer The original $_SERVER superglobal, for restoration after tests.
     */
    protected $originalServer;

    /**
     * @since 1.0.0
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->originalServer = $_SERVER;
    }

    /**
     * @since 1.0.0
     */
    protected function tearDown(): void
    {
        parent::tearDown();
        $_SERVER = $this->originalServer;
    }

    /**
     * @since 1.0.0
     */
    public function testShouldEchoNothingWithNoNotices(): void
    {
        $displayNoticesInAdmin = new DisplayNoticesInAdmin('namespace');

        $this->expectOutputString('');
        $displayNoticesInAdmin();
    }

    /**
     * @since 1.0.0
     */
    public function testShouldAcceptMultipleNotices(): void
    {
        $displayNoticesInAdmin = new DisplayNoticesInAdmin('namespace');
        $notice1 = $this->getSimpleMockNotice('foo');
        $notice2 = $this->getSimpleMockNotice('bar');

        $this->expectOutputString($this->getSimpleNoticeOutput('foo') . $this->getSimpleNoticeOutput('bar'));
        $displayNoticesInAdmin($notice1, $notice2);
    }

    /**
     * @unreleased
     */
    private function getSimpleNoticeOutput(string $content): string
    {
        return "<div class='notice notice-info' data-stellarwp-namespace-notice-id='test_id'>$content</div>";
    }

    /**
     * @unreleased
     */
    public function expectNoticeInOutput(string $expected): void
    {
        $this->expectOutputString($this->getSimpleNoticeOutput($expected));
    }

    /**
     * @unreleased
     */
    private function getSimpleNoticeOutput(string $content): string
    {
        return "<div class='notice notice-info' data-stellarwp-namespace-notice-id='test_id'>$content</div>";
    }

    /**
     * Produces a simple mock with predictable output.
     *
     * @since 1.0.0
     */
    private function getSimpleMockNotice($output): AdminNotice
    {
        return (new AdminNotice('test_id', $output))
            ->withoutAutoParagraph();
    }
}
