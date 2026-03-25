<?php declare(strict_types = 1);

namespace Ozzie\Html\Tests;

use InvalidArgumentException;
use Ozzie\Html\Component;
use Ozzie\Html\Element;
use PHPUnit\Framework\TestCase;
use Stringable;
use stdClass;

class ComponentTest extends TestCase {

    public function test_render(): void
    {
        $component = new Component();
        $this->assertSame('', $component->render());
    }

    public function test_to_string(): void
    {
        $component = new Component();
        $this->assertSame('', (string) $component);
    }

    public function test_add_content(): void
    {
        $component = new Component();
        $component
            ->add_content('foo')
            ->add_content('bar');

        $this->assertSame('foobar', (string) $component);
    }

    public function test_add_content_with_append(): void
    {
        $component = new Component();
        $component
            ->add_content('foo')
            ->add_content('bar', true);

        $this->assertSame('foobar', (string) $component);
    }

    public function test_add_content_with_prepend(): void
    {
        $component = new Component();
        $component
            ->add_content('foo')
            ->add_content('bar', false);

        $this->assertSame('barfoo', (string) $component);
    }

    public function test_content_append(): void
    {
        $component = new Component();
        $component
            ->add_content('foo')
            ->content_append('bar');

        $this->assertSame('foobar', (string) $component);
    }

    public function test_content_prepend(): void
    {
        $component = new Component();
        $component
            ->add_content('foo')
            ->content_prepend('bar');

        $this->assertSame('barfoo', (string) $component);
    }

    public function test_content_set(): void
    {
        $component = new Component();
        $component
            ->add_content('foo')
            ->content_set('bar');

        $this->assertSame('bar', (string) $component);
    }

    public function test_content_set_array(): void
    {
        $component = new Component();
        $component->content_set(['foo', 'bar', 'baz']);
        $this->assertSame('foobarbaz', (string) $component);
    }

    public function test_render_mixed_null(): void
    {
        $component = new Component();
        $this->assertSame('', $component->render_mixed(null));
    }

    public function test_render_mixed_string(): void
    {
        $component = new Component();
        $this->assertSame('foo', $component->render_mixed('foo'));
    }

    public function test_render_mixed_int(): void
    {
        $component = new Component();
        $this->assertSame('42', $component->render_mixed(42));
    }

    public function test_render_mixed_float(): void
    {
        $component = new Component();
        $this->assertSame('3.14', $component->render_mixed(3.14));
    }

    public function test_render_mixed_array(): void
    {
        $component = new Component();
        $this->assertSame('foobarbaz', $component->render_mixed(['foo', 'bar', 'baz']));
    }

    public function test_render_mixed_object(): void
    {
        $component = new Component();
        $this->expectException(InvalidArgumentException::class);
        $component->render_mixed(new stdClass());
    }

    public function test_render_mixed_object_stringable(): void
    {
        $component  = new Component();
        $stringable = new class implements Stringable {

            public function __toString(): string
            {
                return 'foo';
            }

        };
        $this->assertSame('foo', $component->render_mixed($stringable));
    }

    public function test_render_mixed_bool_throws(): void
    {
        $component = new Component();
        $this->expectException(InvalidArgumentException::class);
        $component->render_mixed(true);
    }

    public function test_element(): void
    {
        $element = Component::Element('foo');
        $this->assertInstanceOf(Element::class, $element);
    }

    public function test_add_element(): void
    {
        $component = new Component();
        $result    = $component->add_element('foo');
        $this->assertSame($component, $result);
        $this->assertSame((string) new Element('foo'), (string) $component);
    }

    public function test_new_element(): void
    {
        $component = new Component();
        $result    = $component->new_element('foo');
        $this->assertInstanceOf(Element::class, $result);
        $this->assertSame((string) new Element('foo'), (string) $component);
    }

    public function test_chaining_functions(): void
    {
        $component = new Component();

        $this->assertSame($component, $component->add_content('foo'));
        $this->assertSame($component, $component->content_append('foo'));
        $this->assertSame($component, $component->content_prepend('foo'));
        $this->assertSame($component, $component->content_set('foo'));

        $this->assertSame($component, $component->add_element('foo'));
    }

}
