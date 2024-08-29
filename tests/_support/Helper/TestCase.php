<?php

declare(strict_types=1);

namespace StellarWP\FieldConditions\Tests;

use Codeception\Test\Unit;
use Traversable;

class TestCase extends Unit
{
    protected $backupGlobals = false;

    public static function assertIsIterable($actual, $message = '')
    {
        if (\function_exists('is_iterable') === true) {
            // PHP >= 7.1.
            // phpcs:ignore PHPCompatibility.FunctionUse.NewFunctions.is_iterableFound
            self::assertTrue(is_iterable($actual), $message);
        } else {
            // PHP < 7.1.
            $result = (\is_array($actual) || $actual instanceof Traversable);
            self::assertTrue($result, $message);
        }
    }
}
