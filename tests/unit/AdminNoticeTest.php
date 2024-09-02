<?php

declare(strict_types=1);

namespace StellarWP\AdminNotices\Tests\Unit;

use DateTimeImmutable;
use InvalidArgumentException;
use StellarWP\AdminNotices\AdminNotice;
use StellarWP\AdminNotices\Tests\TestCase;
use StellarWP\AdminNotices\ValueObjects\NoticeUrgency;
use StellarWP\AdminNotices\ValueObjects\ScreenCondition;
use StellarWP\AdminNotices\ValueObjects\UserCapability;

/**
 * @coversDefaultClass \StellarWP\AdminNotices\AdminNotice
 */
class AdminNoticeTest extends TestCase
{
    /**
     * @covers ::__construct
     *
     * @unreleased
     */
    public function testThrowsExceptionWhenRenderIsNotStringOrCallable()
    {
        $this->expectException(InvalidArgumentException::class);
        new AdminNotice(1);
    }

    /**
     * @covers ::ifUserCan
     *
     * @unreleased
     */
    public function testIfUserCan(): void
    {
        $notice = new AdminNotice('test');
        $self = $notice->ifUserCan('test', ['test', 1], ['test', 2, 3]);

        $this->assertCount(3, $notice->getUserCapabilities());
        $this->assertContainsOnlyInstancesOf(UserCapability::class, $notice->getUserCapabilities());
        $this->assertEquals(
            [new UserCapability('test'), new UserCapability('test', [1]), new UserCapability('test', [2, 3])],
            $notice->getUserCapabilities()
        );
        $this->assertSame($notice, $self);
    }

    /**
     * @covers ::ifUserCan
     *
     * @unreleased
     */
    public function testIfUserCanShouldThrowExceptionWhenCapabilityIsNotStringOrArray(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $notice = new AdminNotice('test');
        $notice->ifUserCan(1);
    }

    /**
     * @covers ::ifUserCan
     *
     * @unreleased
     */
    public function testIfUserCanShouldThrowExceptionWhenCapabilityArrayIsMisshaped(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $notice = new AdminNotice('test');
        $notice->ifUserCan([]);
    }

    /**
     * @covers ::after
     * @covers ::parseDate
     * @covers ::getAfterDate
     *
     * @dataProvider dateTestProvider
     *
     * @unreleased
     */
    public function testAfter($parameter, $assertDate): void
    {
        $notice = new AdminNotice('test');
        $self = $notice->after($parameter);
        $this->assertInstanceOf(DateTimeImmutable::class, $notice->getAfterDate());
        $this->assertSame($assertDate, $notice->getAfterDate()->format('Y-m-d'));
        $this->assertSame($notice, $self);
    }

    /**
     * @covers ::until
     * @covers ::parseDate
     * @covers ::getUntilDate
     *
     * @dataProvider dateTestProvider
     *
     * @unreleased
     */
    public function testUntil($parameter, $assertDate): void
    {
        $notice = new AdminNotice('test');
        $self = $notice->until($parameter);
        $this->assertInstanceOf(DateTimeImmutable::class, $notice->getUntilDate());
        $this->assertSame($assertDate, $notice->getUntilDate()->format('Y-m-d'));
        $this->assertSame($notice, $self);
    }

    /**
     * @covers ::between
     *
     * @unreleased
     */
    public function testBetween(): void
    {
        $notice = new AdminNotice('test');
        $self = $notice->between('2021-01-01', '2021-02-01');
        $this->assertInstanceOf(DateTimeImmutable::class, $notice->getAfterDate());
        $this->assertSame('2021-01-01', $notice->getAfterDate()->format('Y-m-d'));
        $this->assertInstanceOf(DateTimeImmutable::class, $notice->getUntilDate());
        $this->assertSame('2021-02-01', $notice->getUntilDate()->format('Y-m-d'));
        $this->assertSame($notice, $self);
    }

    public function dateTestProvider(): array
    {
        return [
            ['2021-01-01', '2021-01-01'], // accepts a string
            [1612137600, '2021-02-01'], // accepts a UNIX timestamp
            [new DateTimeImmutable('2021-01-01'), '2021-01-01'], // accepts a DateTimeInterface object
        ];
    }

    /**
     * @covers ::when
     * @covers ::getWhenCallback
     *
     * @unreleased
     */
    public function testWhen(): void
    {
        $notice = new AdminNotice('test');
        $self = $notice->when(function () {
            return true;
        });

        $this->assertTrue($notice->getWhenCallback()());
        $this->assertSame($notice, $self);
    }

    /**
     * @covers ::on
     * @covers ::getOnConditions
     *
     * @unreleased
     */
    public function testOn(): void
    {
        $notice = new AdminNotice('test');
        $self = $notice->on('test', new ScreenCondition('test2'));

        $this->assertEquals([new ScreenCondition('test'), new ScreenCondition('test2')], $notice->getOnConditions());
        $this->assertSame($notice, $self);
    }

    /**
     * @covers ::autoParagraph
     * @covers ::withoutAutoParagraph
     * @covers ::shouldAutoParagraph
     *
     * @unreleased
     */
    public function testAutoParagraph(): void
    {
        // Defaults to false
        $notice = new AdminNotice('test');
        $this->assertFalse($notice->shouldAutoParagraph());

        // Method defaults to true
        $self = $notice->autoParagraph();
        $this->assertTrue($notice->shouldAutoParagraph());
        $this->assertSame($notice, $self);

        // Method can be set to false
        $notice->autoParagraph(false);
        $this->assertFalse($notice->shouldAutoParagraph());

        // Method can be explicitly set to true
        $notice->autoParagraph(true);
        $this->assertTrue($notice->shouldAutoParagraph());

        // withoutAutoParagraph is an alias for autoParagraph(false)
        $self = $notice->withoutAutoParagraph();
        $this->assertFalse($notice->shouldAutoParagraph());
        $this->assertSame($notice, $self);
    }

    /**
     * @covers ::urgency
     * @covers ::getUrgency
     *
     * @unreleased
     */
    public function testUrgency(): void
    {
        // Defaults to 'info'
        $notice = new AdminNotice('test');
        $this->assertEquals('info', $notice->getUrgency());

        // Can be set with string
        $self = $notice->urgency('error');
        $this->assertEquals('error', $notice->getUrgency());
        $this->assertSame($notice, $self);

        // Can be set with NoticeUrgency object
        $notice->urgency(new NoticeUrgency('warning'));
        $this->assertEquals('warning', $notice->getUrgency());
    }

    /**
     * @covers ::withWrapper
     * @covers ::withoutWrapper
     * @covers ::usesWrapper
     */
    public function testWithWrapper(): void
    {
        // Defaults to true
        $notice = new AdminNotice('test');
        $this->assertTrue($notice->usesWrapper());

        // Method can be set to false
        $self = $notice->withWrapper(false);
        $this->assertFalse($notice->usesWrapper());
        $this->assertSame($notice, $self);

        // Method can be explicitly set to true
        $notice->withWrapper(true);
        $this->assertTrue($notice->usesWrapper());

        // withoutWrapper is an alias for withWrapper(false)
        $self = $notice->withoutWrapper();
        $this->assertFalse($notice->usesWrapper());
        $this->assertSame($notice, $self);
    }

    /**
     * @covers ::dismissible
     * @covers ::notDismissible
     * @covers ::isDismissible
     *
     * @unreleased
     */
    public function testDismissible(): void
    {
        // Defaults to true
        $notice = new AdminNotice('test');
        $this->assertTrue($notice->isDismissible());

        // Method defaults to true
        $self = $notice->dismissible();
        $this->assertTrue($notice->isDismissible());
        $this->assertSame($notice, $self);

        // Method can be explicitly set to false
        $notice->dismissible(false);
        $this->assertFalse($notice->isDismissible());

        // Method can be set to true
        $notice->dismissible(true);
        $this->assertTrue($notice->isDismissible());

        // notDismissible is an alias for dismissible(false)
        $self = $notice->notDismissible();
        $this->assertFalse($notice->isDismissible());
        $this->assertSame($notice, $self);
    }

    /**
     * @covers ::getRenderTextOrCallback
     *
     * @unreleased
     */
    public function testGetRenderTextOrCallback(): void
    {
        // Returns the render text
        $notice = new AdminNotice('test');
        $this->assertSame('test', $notice->getRenderTextOrCallback());

        // Returns the render callback
        $callback = function () {};
        $notice = new AdminNotice($callback);
        $this->assertSame($callback, $notice->getRenderTextOrCallback());
    }

    /**
     * @covers ::getRenderedContent
     *
     * @unreleased
     */
    public function testRenderedContent(): void
    {
        // Returns the plain, rendered text
        $notice = new AdminNotice('test');
        $this->assertSame('test', $notice->getRenderedContent());

        // Returns the text with auto-paragraphs
        $notice = (new AdminNotice('test'))
            ->autoParagraph();
        $this->assertSame(wpautop('test'), $notice->getRenderedContent());

        // Returns the results of the callback
        $notice = new AdminNotice(function () {
            return 'test-callback';
        });
        $this->assertSame('test-callback', $notice->getRenderedContent());
    }

    /**
     * @covers ::getUserCapabilities
     *
     * @unreleased
     */
    public function testGetUserCapabilities(): void
    {
        // Defaults to empty array
        $notice = new AdminNotice('test');
        $this->assertEmpty($notice->getUserCapabilities());

        // Returns the user capabilities
        $notice->ifUserCan('test');
        $this->assertCount(1, $notice->getUserCapabilities());
        $this->assertContainsOnlyInstancesOf(UserCapability::class, $notice->getUserCapabilities());
        $this->assertEquals([new UserCapability('test')], $notice->getUserCapabilities());
    }

    /**
     * @covers ::getAfterDate
     *
     * @unreleased
     */
    public function testGetAfterDate(): void
    {
        // Defaults to null
        $notice = new AdminNotice('test');
        $this->assertNull($notice->getAfterDate());

        // Returns the date after which the notice should be displayed
        $notice->after('2021-01-01');
        $this->assertInstanceOf(DateTimeImmutable::class, $notice->getAfterDate());
        $this->assertSame('2021-01-01', $notice->getAfterDate()->format('Y-m-d'));
    }

    /**
     * @covers ::getUntilDate
     *
     * @unreleased
     */
    public function testGetUntilDate(): void
    {
        // Defaults to null
        $notice = new AdminNotice('test');
        $this->assertNull($notice->getUntilDate());

        // Returns the date until which the notice should be displayed
        $notice->until('2021-01-01');
        $this->assertInstanceOf(DateTimeImmutable::class, $notice->getUntilDate());
        $this->assertSame('2021-01-01', $notice->getUntilDate()->format('Y-m-d'));
    }

    /**
     * @covers ::getWhenCallback
     *
     * @unreleased
     */
    public function testGetWhenCallback(): void
    {
        // Defaults to null
        $notice = new AdminNotice('test');
        $this->assertNull($notice->getWhenCallback());

        // Returns the callback
        $callback = function () {};
        $notice->when($callback);
        $this->assertSame($callback, $notice->getWhenCallback());
    }
}
