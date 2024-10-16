<?php

declare(strict_types=1);


use StellarWP\AdminNotices\Tests\Support\Helper\TestCase;
use StellarWP\AdminNotices\Traits\HasNamespace;

class HasNamespaceTest extends TestCase
{
    public function testShouldHaveNamespaceConstructorParameter(): void
    {
        $class = new ClassWithNamespace('namespace');
        $this->assertEquals('namespace', $class->getNamespace());
    }
}

class ClassWithNamespace
{
    use HasNamespace;

    public function getNamespace(): string
    {
        return $this->namespace;
    }
}
