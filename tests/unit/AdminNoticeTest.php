<?php

declare(strict_types=1);

namespace StellarWP\AdminNotices\Tests\Unit;

use DateTimeImmutable;
use InvalidArgumentException;
use StellarWP\AdminNotices\AdminNotice;
use StellarWP\AdminNotices\Tests\TestCase;
use StellarWP\AdminNotices\ValueObjects\NoticeUrgency;
use StellarWP\AdminNotices\ValueObjects\ScreenCondition;

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

        $this->assertSame([['test'], ['test', 1], ['test', 2, 3]], $notice->getUserCapabilities());
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
}
