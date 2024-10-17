<?php

declare(strict_types=1);

namespace StellarWP\AdminNotices\Tests\Unit\ValueObjects;

use InvalidArgumentException;
use StellarWP\AdminNotices\Tests\Support\Helper\TestCase;
use StellarWP\AdminNotices\ValueObjects\ScreenCondition;

/**
 * @covers \StellarWP\AdminNotices\ValueObjects\ScreenCondition
 */

/**
 * @coversDefaultClass \StellarWP\AdminNotices\ValueObjects\ScreenCondition
 */
class ScreenConditionTest extends TestCase
{
    /**
     * @covers ::isRegex
     *
     * @since 1.0.0
     */
    public function testIsRegex(): void
    {
        // Passes with regex using ~ as the delimiter
        $condition = new ScreenCondition('~/wp-admin/i~');
        $this->assertTrue($condition->isRegex());

        // Fails with regex using / (or any other) as the delimiter
        $condition = new ScreenCondition('/wp-admin/i');
        $this->assertFalse($condition->isRegex());

        // Fails with non-regex string
        $condition = new ScreenCondition('wp-admin');
        $this->assertFalse($condition->isRegex());

        // Fails with WP_Screen comparison array
        $condition = new ScreenCondition(['id' => 'wp-admin']);
        $this->assertFalse($condition->isRegex());
    }

    /**
     * @covers ::getCondition
     *
     * @since 1.0.0
     */
    public function testGetCondition(): void
    {
        $condition = new ScreenCondition('wp-admin');
        $this->assertSame('wp-admin', $condition->getCondition());
    }

    /**
     * @since 1.0.0
     */
    public function testShouldThrowExceptionWithNonStringOrArray(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new ScreenCondition(123);
    }

    /**
     * @since 1.0.0
     */
    public function testShouldThrowExceptionWithNonAssociativeArray(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new ScreenCondition(['wp-admin']);
    }

    /**
     * @since 1.0.0
     */
    public function testShouldThrowExceptionForInvalidWPScreenProperties(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new ScreenCondition(['invalid' => 'wp-admin']);
    }
}
