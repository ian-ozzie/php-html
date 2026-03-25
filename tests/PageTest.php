<?php declare(strict_types = 1);

namespace Ozzie\Html\Tests;

use Ozzie\Html\Page;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class PageTest extends TestCase {

    public function test_construct(): void
    {
        $reflector   = new ReflectionClass(Page::class);
        $constructor = $reflector->getConstructor();
        $this->assertTrue($constructor->isPrivate());
    }

    public function test_get_instance(): void
    {
        $root = Page::get_instance('/');
        $this->assertInstanceOf(Page::class, $root);

        $foo = Page::get_instance('/foo');
        $this->assertInstanceOf(Page::class, $foo);
        $this->assertNotSame($root, $foo);

        $duplicate = Page::get_instance('/');
        $this->assertInstanceOf(Page::class, $duplicate);
        $this->assertSame($root, $duplicate);

        $bar = Page::get_instance('/foo');
        $this->assertInstanceOf(Page::class, $bar);
        $this->assertSame($foo, $bar);
    }

}
