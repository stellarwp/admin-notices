<?php

declare(strict_types=1);

use lucatume\WPBrowser\Traits\UopzFunctions;
use StellarWP\AdminNotices\Actions\RenderAdminNotice;
use StellarWP\AdminNotices\AdminNotice;
use StellarWP\AdminNotices\Tests\Support\Helper\TestCase;

class RenderAdminNoticeTest extends TestCase
{
    use UopzFunctions;

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

    public function testShouldIncludeAttributesForCustomNotices()
    {
        $notice = (new AdminNotice('test_id', function () {
            return '<div>It is Stellar at StellarWP</div>';
        }))
            ->custom();
        $expectedNoticeWithAttributes = "<div data-stellarwp-namespace-notice-id='test_id' data-stellarwp-namespace-location='below_header'>It is Stellar at StellarWP</div>";

        $renderAdminNotice = new RenderAdminNotice('namespace');

        $tagProcessorMock = $this->createMock(TagProcessorMock::class);

        $tagMatcher = $this->exactly(2);
        $tagProcessorMock
            ->expects($tagMatcher)
            ->method('set_attribute')
            ->willReturnCallback(function (string $key, string $value) use ($tagMatcher) {
                switch ($tagMatcher->getInvocationCount()) {
                    case 1:
                        $this->assertEquals('data-stellarwp-namespace-notice-id', $key);
                        break;
                    case 2:
                        $this->assertEquals('data-stellarwp-namespace-location', $key);
                        $this->assertEquals('below_header', $value);
                        break;
                }
            });

        $tagProcessorMock
            ->expects($this->once())
            ->method('next_tag');

        $tagProcessorMock
            ->expects($this->once())
            ->method('__toString')
            ->willReturn($expectedNoticeWithAttributes);

        $this->setClassMock('WP_HTML_Tag_Processor', $tagProcessorMock);

        $this->assertEquals($expectedNoticeWithAttributes, $renderAdminNotice($notice));
    }

    public function testShouldOmitLocationForCustomInPlaceNotices(): void
    {
        $notice = (new AdminNotice('test_id', function () {
            return '<div>It is Stellar at StellarWP</div>';
        }))
            ->custom()
            ->inPlace();

        $expectedNoticeWithAttributes = "<div data-stellarwp-namespace-notice-id='test_id' data-stellarwp-namespace-location='below_header'>It is Stellar at StellarWP</div>";

        $renderAdminNotice = new RenderAdminNotice('namespace');

        $tagProcessorMock = $this->createMock(TagProcessorMock::class);

        $tagProcessorMock
            ->expects($this->once())
            ->method('set_attribute')
            ->with('data-stellarwp-namespace-notice-id');

        $tagProcessorMock
            ->expects($this->once())
            ->method('next_tag');

        $tagProcessorMock
            ->expects($this->once())
            ->method('__toString')
            ->willReturn($expectedNoticeWithAttributes);

        $this->setClassMock('WP_HTML_Tag_Processor', $tagProcessorMock);

        $this->assertEquals($expectedNoticeWithAttributes, $renderAdminNotice($notice));
    }
}

class TagProcessorMock
{
    public function __construct($content)
    {
        $this->content = $content;
    }

    public function next_tag(): self
    {
        return $this;
    }

    public function set_attribute($name, $value) {}

    public function __toString()
    {
        return '';
    }
}
