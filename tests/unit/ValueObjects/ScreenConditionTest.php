<?php

declare(strict_types=1);

namespace StellarWP\AdminNotices\Tests\Unit\ValueObjects;

use InvalidArgumentException;
use StellarWP\AdminNotices\Tests\TestCase;
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
     * @unreleased
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
     * @unreleased
     */
    public function testGetCondition(): void
    {
        $condition = new ScreenCondition('wp-admin');
        $this->assertSame('wp-admin', $condition->getCondition());
    }

    /**
     * @unreleased
     */
    public function testShouldThrowExceptionWithNonStringOrArray(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new ScreenCondition(123);
    }

    /**
     * @unreleased
     */
    public function testShouldThrowExceptionWithNonAssociativeArray(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new ScreenCondition(['wp-admin']);
    }

    /**
     * @unreleased
     */
    public function testShouldThrowExceptionForInvalidWPScreenProperties(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new ScreenCondition(['invalid' => 'wp-admin']);
    }
}
