<?php

declare(strict_types=1);

use Ozzie\Html\Component;
use Ozzie\Html\Element;

test('render', function () {
    $component = new Component;
    expect($component->render())->toBe('');
});

test('to_string', function () {
    $component = new Component;
    expect((string) $component)->toBe('');
});

test('add_content', function () {
    $component = new Component;
    $component
        ->add_content('foo')
        ->add_content('bar');

    expect((string) $component)->toBe('foobar');
});

test('prepend_content', function () {
    $component = new Component;
    $component
        ->add_content('foo')
        ->prepend_content('bar');

    expect((string) $component)->toBe('barfoo');
});

test('set_content', function () {
    $component = new Component;
    $component
        ->add_content('foo')
        ->set_content('bar');

    expect((string) $component)->toBe('bar');
});

test('set_content_array', function () {
    $component = new Component;
    $component->set_content(['foo', 'bar', 'baz']);
    expect((string) $component)->toBe('foobarbaz');
});

test('render_mixed_null', function () {
    $component = new Component;
    expect($component->render_mixed(null))->toBe('');
});

test('render_mixed_string', function () {
    $component = new Component;
    expect($component->render_mixed('foo'))->toBe('foo');
});

test('render_mixed_int', function () {
    $component = new Component;
    expect($component->render_mixed(42))->toBe('42');
});

test('render_mixed_float', function () {
    $component = new Component;
    expect($component->render_mixed(3.14))->toBe('3.14');
});

test('render_mixed_array', function () {
    $component = new Component;
    expect($component->render_mixed(['foo', 'bar', 'baz']))->toBe('foobarbaz');
});

test('render_mixed_object', function () {
    $component = new Component;
    expect(fn () => $component->render_mixed(new stdClass))->toThrow(InvalidArgumentException::class);
});

test('render_mixed_object_stringable', function () {
    $component = new Component;
    $stringable = new class implements Stringable
    {
        public function __toString(): string
        {
            return 'foo';
        }
    };
    expect($component->render_mixed($stringable))->toBe('foo');
});

test('render_mixed_bool_throws', function () {
    $component = new Component;
    expect(fn () => $component->render_mixed(true))->toThrow(InvalidArgumentException::class);
});

test('element', function () {
    $element = Component::Element('foo');
    expect($element)->toBeInstanceOf(Element::class);
});

test('add_element', function () {
    $component = new Component;
    $result = $component->add_element('foo');
    expect($result)->toBe($component);
    expect((string) $component)->toBe((string) new Element('foo'));
});

test('new_element', function () {
    $component = new Component;
    $result = $component->new_element('foo');
    expect($result)->toBeInstanceOf(Element::class);
    expect((string) $component)->toBe((string) new Element('foo'));
});

test('chaining_functions', function () {
    $component = new Component;

    expect($component->add_content('foo'))->toBe($component);
    expect($component->prepend_content('foo'))->toBe($component);
    expect($component->set_content('foo'))->toBe($component);

    expect($component->add_element('foo'))->toBe($component);
});

test('cache_render_defaults_to_false', function () {
    $component = new Component;
    expect($component->cache_render)->toBeFalse();
});

test('cache_render_returns_fresh_output_when_disabled', function () {
    $component = new Component;
    $component->add_content('foo');
    expect($component->render())->toBe('foo');

    $component->add_content('bar');
    expect($component->render())->toBe('foobar');
});

test('cache_render_returns_cached_output_when_enabled', function () {
    $component = new Component;
    $component->cache_render = true;
    $component->add_content('foo');
    expect($component->render())->toBe('foo');

    $component->add_content('bar');
    expect($component->render())->toBe('foo');
});

test('cache_render_same_instance_rendered_twice', function () {
    $component = new Component;
    $component->cache_render = true;
    $component->add_content('hello');

    $parent = new Component;
    $parent->add_content($component);
    $parent->add_content($component);

    expect($parent->render())->toBe('hellohello');
});

test('cache_render_prevents_render_time_composition_duplication', function () {
    $component = new class extends Component
    {
        public function render(): string
        {
            $this->add_content('composed ');

            return parent::render();
        }
    };

    $component->cache_render = true;
    expect($component->render())->toBe('composed ');
    expect($component->render())->toBe('composed ');
});

test('cache_render_without_cache_duplicates_render_time_composition', function () {
    $component = new class extends Component
    {
        public function render(): string
        {
            $this->add_content('composed ');

            return parent::render();
        }
    };

    expect($component->render())->toBe('composed ');
    expect($component->render())->toBe('composed composed ');
});

test('cache_render_empty_first_render_is_sticky', function () {
    $component = new Component;
    $component->cache_render = true;
    expect($component->render())->toBe('');

    $component->add_content('foo');
    expect($component->render())->toBe('');
});

test('cache_render_content_mutation_after_warm_cache_is_ignored', function () {
    $component = new Component;
    $component->cache_render = true;
    $component->add_content('foo');
    $component->render();

    $component->set_content('bar');
    expect($component->render())->toBe('foo');
});

test('cache_render_toggled_off_returns_live_content', function () {
    $component = new Component;
    $component->cache_render = true;
    $component->add_content('foo');
    $component->render();

    $component->cache_render = false;
    $component->add_content('bar');
    expect($component->render())->toBe('foobar');
});
