<?php

declare(strict_types=1);

use StellarWP\AdminNotices\Actions\RenderAdminNotice;
use StellarWP\AdminNotices\AdminNotice;
use StellarWP\AdminNotices\Tests\TestCase;

class RenderAdminNoticeTest extends TestCase
{
    /**
     * @unreleased
     */
    public function testShouldRenderNoticeWithoutWrapper(): void
    {
        $notice = (new AdminNotice('Hello world!'))
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
        $notice = (new AdminNotice('Hello world!'))
            ->withoutAutoParagraph()
            ->notDismissible();

        $renderAdminNotice = new RenderAdminNotice($notice);

        $this->assertEquals(
            "<div class='notice notice-info'>Hello world!</div>",
            $renderAdminNotice()
        );
    }

    /**
     * @unreleased
     */
    public function testShouldIncludeDismissibleClass(): void
    {
        $notice = (new AdminNotice('Hello world!'))
            ->withoutAutoParagraph()
            ->dismissible();

        $renderAdminNotice = new RenderAdminNotice($notice);

        $this->assertEquals(
            "<div class='notice notice-info is-dismissible'>Hello world!</div>",
            $renderAdminNotice()
        );
    }

    /**
     * @unreleased
     */
    public function testShouldIncludeAutoParagraphs(): void
    {
        $notice = (new AdminNotice('Hello world!'))
            ->autoParagraph()
            ->notDismissible();

        $renderAdminNotice = new RenderAdminNotice($notice);
        $textWithAutoParagraphs = wpautop('Hello world!');

        $this->assertEquals(
            "<div class='notice notice-info'>$textWithAutoParagraphs</div>",
            $renderAdminNotice()
        );
    }

    /**
     * @unreleased
     */
    public function testShouldRenderCallbackOutput(): void
    {
        $notice = (new AdminNotice(function () {
            return 'Hello world!';
        }))
            ->withoutAutoParagraph()
            ->notDismissible();

        $renderAdminNotice = new RenderAdminNotice($notice);

        $this->assertEquals(
            "<div class='notice notice-info'>Hello world!</div>",
            $renderAdminNotice()
        );
    }
}
