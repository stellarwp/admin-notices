<?php

declare(strict_types=1);


namespace StellarWP\AdminNotices\Tests\Unit\ValueObjects;

use InvalidArgumentException;
use StellarWP\AdminNotices\Tests\Support\Helper\TestCase;
use StellarWP\AdminNotices\ValueObjects\NoticeLocation;

/**
 * @coversDefaultClass \StellarWP\AdminNotices\ValueObjects\NoticeLocation
 */
class NoticeLocationTest extends TestCase
{
    /**
     * @covers ::aboveHeader
     *
     * @unreleased
     */
    public function testAboveHeader(): void
    {
        $location = NoticeLocation::aboveHeader();

        $this->assertEquals('above_header', $location);
    }

    /**
     * @covers ::standard
     *
     * @unreleased
     */
    public function testStandard(): void
    {
        $location = NoticeLocation::standard();

        $this->assertEquals('below_header', $location);
    }

    /**
     * @covers ::belowHeader
     *
     * @unreleased
     */
    public function testBelowHeader(): void
    {
        $location = NoticeLocation::belowHeader();

        $this->assertEquals('below_header', $location);
    }

    /**
     * @covers ::inline
     *
     * @unreleased
     */
    public function testInline(): void
    {
        $location = NoticeLocation::inline();

        $this->assertEquals('inline', $location);
    }

    /**
     * @covers ::__toString
     *
     * @unreleased
     */
    public function testToString(): void
    {
        $location = new NoticeLocation('above_header');

        $this->assertSame('above_header', (string)$location);
    }

    /**
     * @dataProvider constructorValidationDataProvider
     * @covers ::__construct
     */
    public function testConstructorValidation($value, $shouldPass): void
    {
        if ($shouldPass) {
            $this->assertInstanceOf(NoticeLocation::class, new NoticeLocation($value));
        } else {
            $this->expectException(InvalidArgumentException::class);
            new NoticeLocation($value);
        }
    }

    /**
     * @unreleased
     *
     * @return array<string, array{0: string, 1: bool}>
     */
    public function constructorValidationDataProvider(): array
    {
        return [
            'above header is valid' => ['above_header', true],
            'below header is valid' => ['below_header', true],
            'inline is valid' => ['inline', true],
            'standard is invalid' => ['standard', false],
            'empty string is invalid' => ['', false],
        ];
    }
}
