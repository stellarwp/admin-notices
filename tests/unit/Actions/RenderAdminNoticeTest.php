<?php

declare(strict_types=1);

use StellarWP\AdminNotices\Actions\RenderAdminNotice;
use StellarWP\AdminNotices\AdminNotice;
use StellarWP\AdminNotices\Tests\Support\Helper\TestCase;

class RenderAdminNoticeTest extends TestCase
{
    /**
     * @since 1.0.0
     */
    public function testShouldRenderNoticeWithoutWrapper(): void
    {
        $notice = (new AdminNotice('test_id', 'Hello world!'))
            ->withoutAutoParagraph();

        $renderAdminNotice = new RenderAdminNotice('namespace');

        $this->assertEquals(
            '<div class=\'notice notice-info\' data-stellarwp-namespace-notice-id=\'test_id\'>Hello world!</div>',
            $renderAdminNotice($notice)
        );
    }

    /**
     * Tests the wrapper and that the default urgency is info
     *
     * @since 1.0.0
     */
    public function testShouldRenderNoticeInWrapper(): void
    {
        $notice = (new AdminNotice('test_id', 'Hello world!'))
            ->withoutAutoParagraph()
            ->notDismissible();

        $renderAdminNotice = new RenderAdminNotice('namespace');

        $this->assertEquals(
            "<div class='notice notice-info' data-stellarwp-namespace-notice-id='test_id'>Hello world!</div>",
            $renderAdminNotice($notice)
        );
    }

    /**
     * @since 1.0.0
     */
    public function testShouldIncludeDismissibleClass(): void
    {
        $notice = (new AdminNotice('test_id', 'Hello world!'))
            ->withoutAutoParagraph()
            ->dismissible();

        $renderAdminNotice = new RenderAdminNotice('namespace');

        $this->assertEquals(
            "<div class='notice notice-info is-dismissible' data-stellarwp-namespace-notice-id='test_id'>Hello world!</div>",
            $renderAdminNotice($notice)
        );
    }

    /**
     * @since 1.0.0
     */
    public function testShouldIncludeAutoParagraphs(): void
    {
        $notice = (new AdminNotice('test_id', 'Hello world!'))
            ->autoParagraph()
            ->notDismissible();

        $renderAdminNotice = new RenderAdminNotice('namespace');
        $textWithAutoParagraphs = wpautop('Hello world!');

        $this->assertEquals(
            "<div class='notice notice-info' data-stellarwp-namespace-notice-id='test_id'>$textWithAutoParagraphs</div>",
            $renderAdminNotice($notice)
        );
    }

    /**
     * @since 1.0.0
     */
    public function testShouldRenderCallbackOutput(): void
    {
        $notice = (new AdminNotice('test_id', function () {
            return 'Hello world!';
        }))
            ->withoutAutoParagraph()
            ->notDismissible();

        $renderAdminNotice = new RenderAdminNotice('namespace');

        $this->assertEquals(
            "<div class='notice notice-info' data-stellarwp-namespace-notice-id='test_id'>Hello world!</div>",
            $renderAdminNotice($notice)
        );
    }
}
