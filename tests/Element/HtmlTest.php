<?php

declare(strict_types=1);

namespace Ozzie\Html\Tests;

use Ozzie\Html\Element\Html;
use PHPUnit\Framework\TestCase;

class HtmlTest extends TestCase
{
    public function test_construct_render(): void
    {
        $element = new Html;
        $this->assertSame('<!DOCTYPE html><html><body></body></html>', (string) $element);
    }

    public function test_content(): void
    {
        $element = new Html;
        $element->add_content('Hello World');
        $this->assertSame('<!DOCTYPE html><html><body>Hello World</body></html>', (string) $element);
        $element->content_append('!');
        $this->assertSame('<!DOCTYPE html><html><body>Hello World!</body></html>', (string) $element);
        $element->content_prepend('!');
        $this->assertSame('<!DOCTYPE html><html><body>!Hello World!</body></html>', (string) $element);
        $element->content_set('foo');
        $this->assertSame('<!DOCTYPE html><html><body>foo</body></html>', (string) $element);
    }

    public function test_add_element(): void
    {
        $element = new Html;
        $result = $element->add_element('span');
        $this->assertSame($result, $element);
        $this->assertSame('<!DOCTYPE html><html><body><span></span></body></html>', (string) $element);
    }

    public function test_new_element(): void
    {
        $element = new Html;
        $result = $element->new_element('span');
        $this->assertNotSame($result, $element);
        $this->assertSame('<!DOCTYPE html><html><body><span></span></body></html>', (string) $element);
    }
}
