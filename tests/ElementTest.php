<?php declare(strict_types = 1);

namespace Ozzie\Html\Tests;

use Ozzie\Html\Element;
use PHPUnit\Framework\TestCase;

class ElementTest extends TestCase {

    public function test_construct(): void
    {
        $element = new Element('span');
        $this->assertSame('<span></span>', (string) $element);
    }

    public function test_element_with_attribute(): void
    {
        $element = new Element('span', ['hello' => 'world']);
        $this->assertSame('<span hello="world"></span>', (string) $element);
    }

    public function test_construct_with_content(): void
    {
        $element = new Element('span', content: 'foo');
        $this->assertSame('<span>foo</span>', (string) $element);
    }

    public function test_construct_with_attribute_and_content(): void
    {
        $element = new Element('span', ['hello' => 'world'], 'foo');
        $this->assertSame('<span hello="world">foo</span>', (string) $element);
    }

    public function test_get_control(): void
    {
        $element = new Element('span');
        $this->assertSame(false, $element->get_control('void'));
        $this->assertSame(true, $element->get_control('render_empty'));
    }

    public function test_get_control_with_constructor(): void
    {
        $element = new Element('span', ['_controls' => ['void' => true]]);
        $this->assertSame(true, $element->get_control('void'));
        $this->assertSame(true, $element->get_control('render_empty'));
    }

    public function test_set_control(): void
    {
        $element = new Element('span');
        $element->set_control('void', true);
        $this->assertSame(true, $element->get_control('void'));
        $this->assertSame(true, $element->get_control('render_empty'));
    }

    public function test_set_controls(): void
    {
        $element = new Element('span');
        $element->set_controls(['void' => true, 'render_empty' => false]);
        $this->assertSame(true, $element->get_control('void'));
        $this->assertSame(false, $element->get_control('render_empty'));
    }

    public function test_add_class(): void
    {
        $element = new Element('span');
        $element->add_class('foo');
        $this->assertSame('<span class="foo"></span>', (string) $element);
    }

    public function test_add_class_with_constructor(): void
    {
        $element = new Element('span', ['class' => 'foo']);
        $this->assertSame('<span class="foo"></span>', (string) $element);
    }

    public function test_add_classes(): void
    {
        $element = new Element('span');
        $element->add_classes(['foo', 'bar']);
        $this->assertSame('<span class="foo bar"></span>', (string) $element);
    }

    public function test_add_classes_with_constructor(): void
    {
        $element = new Element('span', ['class' => ['foo', 'bar']]);
        $this->assertSame('<span class="foo bar"></span>', (string) $element);
    }

    public function test_set_classes(): void
    {
        $element = new Element('span', ['class' => 'baz']);
        $element->set_classes(['foo', 'bar']);
        $this->assertSame('<span class="foo bar"></span>', (string) $element);
    }

    public function test_add_attribute(): void
    {
        $element = new Element('span');
        $element->add_attribute('hello', 'world');
        $this->assertSame('<span hello="world"></span>', (string) $element);
    }

    public function test_add_attribute_null_empty(): void
    {
        $element = new Element('span');
        $element->add_attribute('hello', null);
        $element->add_attribute('world', '');
        $this->assertSame('<span hello world></span>', (string) $element);
    }

    public function test_add_attributes(): void
    {
        $element = new Element('span');
        $element->add_attributes(['foo' => '', 'hello' => 'world']);
        $this->assertSame('<span foo hello="world"></span>', (string) $element);
    }

    public function test_add_attributes_order(): void
    {
        $element = new Element('span');
        $element->add_attributes(['hello' => 'world', 'foo' => '']);
        $this->assertSame('<span foo hello="world"></span>', (string) $element);
    }

    public function test_get_attributes(): void
    {
        $element = new Element('span');
        $element->add_attributes(['foo' => '', 'hello' => 'world']);
        $this->assertSame('', $element->get_attribute('foo'));
        $this->assertSame('world', $element->get_attribute('hello'));

        $this->assertSame(null, $element->get_attribute('unknown'));
    }

    public function test_set_attributes(): void
    {
        $element = new Element('span', ['foo' => 'bar']);
        $this->assertSame('<span foo="bar"></span>', (string) $element);
        $element->set_attributes(['hello' => 'world']);
        $this->assertSame('<span hello="world"></span>', (string) $element);
    }

    public function test_render(): void
    {
        $element = new Element('span');
        $this->assertSame('<span></span>', $element->render());
    }

    public function test_render_auto_void(): void
    {
        $element = new Element('br');
        $this->assertSame('<br>', $element->render());
    }

    public function test_render_manual_void(): void
    {
        $element = new Element('span', ['_controls' => ['void' => true]]);
        $this->assertSame('<span>', $element->render());
    }

    public function test_render_when_empty(): void
    {
        $element = new Element('span');
        $this->assertSame('<span></span>', $element->render());
        $element->set_control('render_empty', false);
        $this->assertSame('', $element->render());
    }

    public function test_render_open(): void
    {
        $element = new Element('span');
        $this->assertSame('<span>', $element->render_open());
        $element->add_class('test');
        $this->assertSame('<span class="test">', $element->render_open());
        $element->add_attribute('hello', 'world');
        $this->assertSame('<span class="test" hello="world">', $element->render_open());
        $element->add_attribute('foo', '');
        $this->assertSame('<span class="test" foo hello="world">', $element->render_open());
    }

    public function test_render_close(): void
    {
        $element = new Element('span');
        $this->assertSame('</span>', $element->render_close());
    }

}
