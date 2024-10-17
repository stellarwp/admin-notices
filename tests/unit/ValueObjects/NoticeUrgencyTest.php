<?php

declare(strict_types=1);


namespace StellarWP\AdminNotices\Tests\Unit\ValueObjects;

use InvalidArgumentException;
use StellarWP\AdminNotices\Tests\Support\Helper\TestCase;
use StellarWP\AdminNotices\ValueObjects\NoticeUrgency;

/**
 * @coversDefaultClass \StellarWP\AdminNotices\ValueObjects\NoticeUrgency
 */
class NoticeUrgencyTest extends TestCase
{
    /**
     * @covers ::info
     * @since 1.0.0
     */
    public function testInfo(): void
    {
        $this->assertEquals('info', NoticeUrgency::info());
    }

    /**
     * @covers ::warning
     * @since 1.0.0
     */
    public function testWarning(): void
    {
        $this->assertEquals('warning', NoticeUrgency::warning());
    }

    /**
     * @covers ::error
     * @since 1.0.0
     */
    public function testError(): void
    {
        $this->assertEquals('error', NoticeUrgency::error());
    }

    /**
     * @covers ::success
     * @since 1.0.0
     */
    public function testSuccess(): void
    {
        $this->assertEquals('success', NoticeUrgency::success());
    }

    /**
     * @covers ::__toString
     * @since 1.0.0
     */
    public function testToString(): void
    {
        $this->assertSame('info', (string)new NoticeUrgency('info'));
    }

    /**
     * @covers ::__construct
     * @dataProvider constructorTestDataProvider
     *
     * @since 1.0.0
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
     * @since 1.0.0
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
