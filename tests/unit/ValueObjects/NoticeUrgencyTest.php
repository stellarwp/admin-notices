<?php

declare(strict_types=1);


namespace StellarWP\AdminNotices\Tests\Unit\ValueObjects;

use InvalidArgumentException;
use StellarWP\AdminNotices\Tests\TestCase;
use StellarWP\AdminNotices\ValueObjects\NoticeUrgency;

/**
 * @coversDefaultClass \StellarWP\AdminNotices\ValueObjects\NoticeUrgency
 */
class NoticeUrgencyTest extends TestCase
{
    /**
     * @covers ::info
     * @unreleased
     */
    public function testInfo(): void
    {
        $this->assertEquals('info', NoticeUrgency::info());
    }

    /**
     * @covers ::warning
     * @unreleased
     */
    public function testWarning(): void
    {
        $this->assertEquals('warning', NoticeUrgency::warning());
    }

    /**
     * @covers ::error
     * @unreleased
     */
    public function testError(): void
    {
        $this->assertEquals('error', NoticeUrgency::error());
    }

    /**
     * @covers ::success
     * @unreleased
     */
    public function testSuccess(): void
    {
        $this->assertEquals('success', NoticeUrgency::success());
    }

    /**
     * @covers ::__toString
     * @unreleased
     */
    public function testToString(): void
    {
        $this->assertSame('info', (string)new NoticeUrgency('info'));
    }

    /**
     * @covers ::__construct
     * @dataProvider constructorTestDataProvider
     *
     * @unreleased
     */
    public function testConstructorValidation($value, $shouldPass): void
    {
        if ($shouldPass) {
            $this->assertInstanceOf(NoticeUrgency::class, new NoticeUrgency($value));
        } else {
            $this->expectException(InvalidArgumentException::class);
            new NoticeUrgency($value);
        }
    }

    /**
     * @unreleased
     */
    public function constructorTestDataProvider(): array
    {
        return [
            ['info', true],
            ['warning', true],
            ['error', true],
            ['success', true],
            ['invalid', false],
        ];
    }
}
