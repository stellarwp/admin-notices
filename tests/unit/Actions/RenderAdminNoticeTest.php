<?php

declare(strict_types=1);

use StellarWP\AdminNotices\Actions\RenderAdminNotice;
use StellarWP\AdminNotices\AdminNotice;
use StellarWP\AdminNotices\Tests\Support\Helper\TestCase;

class RenderAdminNoticeTest extends TestCase
{
    /**
     * @unreleased
     */
    public function testShouldRenderNoticeWithoutWrapper(): void
    {
        $notice = (new AdminNotice('test_id', 'Hello world!'))
            ->withoutAutoParagraph()
            ->withoutWrapper();

        $renderAdminNotice = new RenderAdminNotice($notice);

        $this->assertEquals('Hello world!', $renderAdminNotice());
    }

    /**
     * Tests the wrapper and that the default urgency is info
     *
     * @unreleased
     */
    public function testShouldRenderNoticeInWrapper(): void
    {
        $notice = (new AdminNotice('test_id', 'Hello world!'))
            ->withoutAutoParagraph()
            ->notDismissible();

        $renderAdminNotice = new RenderAdminNotice($notice);

        $this->assertEquals(
            "<div class='notice notice-info' data-stellarwp-notice-id='test_id'>Hello world!</div>",
            $renderAdminNotice()
        );
    }

    /**
     * @unreleased
     */
    public function testShouldIncludeDismissibleClass(): void
    {
        $notice = (new AdminNotice('test_id', 'Hello world!'))
            ->withoutAutoParagraph()
            ->dismissible();

        $renderAdminNotice = new RenderAdminNotice($notice);

        $this->assertEquals(
            "<div class='notice notice-info is-dismissible' data-stellarwp-notice-id='test_id'>Hello world!</div>",
            $renderAdminNotice()
        );
    }

    /**
     * @unreleased
     */
    public function testShouldIncludeAutoParagraphs(): void
    {
        $notice = (new AdminNotice('test_id', 'Hello world!'))
            ->autoParagraph()
            ->notDismissible();

        $renderAdminNotice = new RenderAdminNotice($notice);
        $textWithAutoParagraphs = wpautop('Hello world!');

        $this->assertEquals(
            "<div class='notice notice-info' data-stellarwp-notice-id='test_id'>$textWithAutoParagraphs</div>",
            $renderAdminNotice()
        );
    }

    /**
     * @unreleased
     */
    public function testShouldRenderCallbackOutput(): void
    {
        $notice = (new AdminNotice('test_id', function () {
            return 'Hello world!';
        }))
            ->withoutAutoParagraph()
            ->notDismissible();

        $renderAdminNotice = new RenderAdminNotice($notice);

        $this->assertEquals(
            "<div class='notice notice-info' data-stellarwp-notice-id='test_id'>Hello world!</div>",
            $renderAdminNotice()
        );
    }
}
