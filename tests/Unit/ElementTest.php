<?php

declare(strict_types=1);

use Ozzie\Html\ComponentTrait;
use Ozzie\Html\Element;
use Ozzie\Html\ElementInterface;
use Ozzie\Html\ElementTrait;

describe('__construct()', function () {
    it('renders an empty tag by default', function () {
        $element = new Element('span');

        expect((string) $element)->toBe('<span></span>');
    });

    it('applies initial attributes', function () {
        $element = new Element('span', ['hello' => 'world']);

        expect((string) $element)->toBe('<span hello="world"></span>');
    });

    it('applies initial content', function () {
        $element = new Element('span', content: 'foo');

        expect((string) $element)->toBe('<span>foo</span>');
    });

    it('applies initial attributes and content together', function () {
        $element = new Element('span', ['hello' => 'world'], 'foo');

        expect((string) $element)->toBe('<span hello="world">foo</span>');
    });

    it('marks known void tags as void elements', function () {
        $element = new Element('br');

        expect($element->render())->toBe('<br>');
    });
});

describe('get_tag()', function () {
    it('returns the element tag name', function () {
        $element = new Element('span');

        expect($element->get_tag())->toBe('span');
    });
});

describe('get_control()', function () {
    it('returns default control values', function () {
        $element = new Element('span');

        expect($element->get_control('void'))->toBe(false);
        expect($element->get_control('render_empty'))->toBe(true);
    });

    it('returns controls provided through constructor attributes', function () {
        $element = new Element('span', ['_controls' => ['void' => true]]);

        expect($element->get_control('void'))->toBe(true);
        expect($element->get_control('render_empty'))->toBe(true);
    });

    it('throws when the control key is unknown', function () {
        $element = new Element('span');

        expect(fn () => $element->get_control('unknown'))->toThrow(InvalidArgumentException::class);
    });
});

describe('set_control()', function () {
    it('updates one control without changing the others', function () {
        $element = new Element('span');
        $element->set_control('void', true);

        expect($element->get_control('void'))->toBe(true);
        expect($element->get_control('render_empty'))->toBe(true);
    });

    it('throws when the control key is unknown', function () {
        $element = new Element('span');

        expect(fn () => $element->set_control('unknown', true))->toThrow(InvalidArgumentException::class);
    });
});

describe('set_controls()', function () {
    it('updates multiple controls at once', function () {
        $element = new Element('span');
        $element->set_controls(['void' => true, 'render_empty' => false]);

        expect($element->get_control('void'))->toBe(true);
        expect($element->get_control('render_empty'))->toBe(false);
    });

    it('throws when any control key is unknown', function () {
        $element = new Element('span');

        expect(fn () => $element->set_controls(['void' => true, 'unknown_key' => true]))->toThrow(InvalidArgumentException::class);
    });
});

describe('get_classes()', function () {
    it('returns classes in insertion order', function () {
        $element = new Element('span', ['class' => 'foo']);
        $element->add_class('bar');

        expect($element->get_classes())->toBe(['foo', 'bar']);
    });

    it('keeps insertion order even though rendering sorts classes', function () {
        $element = new Element('span');
        $element->add_classes(['zebra', 'alpha', 'mango']);

        expect($element->get_classes())->toBe(['zebra', 'alpha', 'mango']);
    });
});

describe('has_class()', function () {
    it('detects whether a class has been added', function () {
        $element = new Element('span', ['class' => 'foo']);

        expect($element->has_class('foo'))->toBeTrue();
        expect($element->has_class('bar'))->toBeFalse();
    });
});

describe('add_class()', function () {
    it('adds a single class to rendered attributes', function () {
        $element = new Element('span');
        $element->add_class('foo');

        expect((string) $element)->toBe('<span class="foo"></span>');
    });

    it('accepts classes supplied through constructor attributes', function () {
        $element = new Element('span', ['class' => 'foo']);

        expect((string) $element)->toBe('<span class="foo"></span>');
    });

    it('ignores boolean class values', function () {
        $element = new Element('span');
        $element->add_attribute('class', true);
        $element->add_attribute('class', false);

        expect($element->get_classes())->toBe([]);
    });
});

describe('add_classes()', function () {
    it('adds multiple classes from an array', function () {
        $element = new Element('span');
        $element->add_classes(['foo', 'bar']);

        expect((string) $element)->toBe('<span class="bar foo"></span>');
    });

    it('adds multiple classes from a space separated string', function () {
        $element = new Element('span');
        $element->add_classes('foo bar');

        expect((string) $element)->toBe('<span class="bar foo"></span>');
    });

    it('accepts class arrays supplied through constructor attributes', function () {
        $element = new Element('span', ['class' => ['foo', 'bar']]);

        expect((string) $element)->toBe('<span class="bar foo"></span>');
    });

    it('filters empty strings', function () {
        $element = new Element('span');
        $element->add_classes(['foo', '', 'bar']);

        expect((string) $element)->toBe('<span class="bar foo"></span>');
    });

    it('skips boolean values in class arrays', function () {
        $element = new Element('span');
        $element->add_attribute('class', ['foo', true, 'bar', false]);

        expect($element->get_classes())->toBe(['foo', 'bar']);
    });
});

describe('set_classes()', function () {
    it('replaces existing classes from an array', function () {
        $element = new Element('span', ['class' => 'baz']);
        $element->set_classes(['foo', 'bar']);

        expect((string) $element)->toBe('<span class="bar foo"></span>');
    });

    it('replaces existing classes from a space separated string', function () {
        $element = new Element('span', ['class' => 'baz']);
        $element->set_classes('foo bar');

        expect((string) $element)->toBe('<span class="bar foo"></span>');
    });

    it('deduplicates class names', function () {
        $element = new Element('span');
        $element->set_classes(['foo', 'bar', 'foo']);

        expect((string) $element)->toBe('<span class="bar foo"></span>');
    });

    it('filters empty class names', function () {
        $element = new Element('span');
        $element->set_classes(['foo', '', 'bar']);

        expect((string) $element)->toBe('<span class="bar foo"></span>');
    });
});

describe('add_attribute()', function () {
    it('adds a scalar attribute', function () {
        $element = new Element('span');
        $element->add_attribute('hello', 'world');

        expect((string) $element)->toBe('<span hello="world"></span>');
    });

    it('skips null and false attributes when rendering', function () {
        $element = new Element('span');
        $element->add_attribute('hello', null);
        $element->add_attribute('world', false);

        expect((string) $element)->toBe('<span></span>');
    });

    it('renders true boolean attributes and empty string attributes', function () {
        $element = new Element('span');
        $element->add_attribute('checked', true);
        $element->add_attribute('readonly', '');

        expect((string) $element)->toBe('<span checked readonly=""></span>');
    });

    it('ignores non-array control attributes', function () {
        $element = new Element('span');
        $result = $element->add_attribute('_controls', 'invalid');

        expect($result)->toBe($element);
        expect((string) $element)->toBe('<span></span>');
    });
});

describe('add_attributes()', function () {
    it('adds several attributes at once', function () {
        $element = new Element('span');
        $element->add_attributes(['foo' => '', 'hello' => 'world']);

        expect((string) $element)->toBe('<span foo="" hello="world"></span>');
    });

    it('renders attributes sorted by key', function () {
        $element = new Element('span');
        $element->add_attributes(['hello' => 'world', 'foo' => '', 'bar' => true]);

        expect((string) $element)->toBe('<span bar foo="" hello="world"></span>');
    });
});

describe('has_attribute()', function () {
    it('detects whether an attribute has been added', function () {
        $element = new Element('span');
        $element->add_attribute('hello', 'world');

        expect($element->has_attribute('hello'))->toBeTrue();
        expect($element->has_attribute('unknown'))->toBeFalse();
    });
});

describe('get_attribute()', function () {
    it('returns stored attribute values and null for missing attributes', function () {
        $element = new Element('span');
        $element->add_attributes(['foo' => '', 'hello' => 'world']);

        expect($element->get_attribute('foo'))->toBe('');
        expect($element->get_attribute('hello'))->toBe('world');
        expect($element->get_attribute('unknown'))->toBeNull();
    });
});

describe('set_attributes()', function () {
    it('replaces existing attributes', function () {
        $element = new Element('span', ['foo' => 'bar']);
        expect((string) $element)->toBe('<span foo="bar"></span>');

        $element->set_attributes(['hello' => 'world']);

        expect((string) $element)->toBe('<span hello="world"></span>');
    });

    it('resets classes while replacing attributes', function () {
        $element = new Element('span', ['class' => 'foo']);
        $element->set_attributes(['class' => 'bar']);

        expect((string) $element)->toBe('<span class="bar"></span>');
    });
});

describe('render()', function () {
    it('renders open and closing tags for normal elements', function () {
        $element = new Element('span');

        expect($element->render())->toBe('<span></span>');
    });

    it('renders only an opening tag for manual void elements', function () {
        $element = new Element('span', ['_controls' => ['void' => true]]);

        expect($element->render())->toBe('<span>');
    });

    it('can omit empty non-void elements', function () {
        $element = new Element('span');
        expect($element->render())->toBe('<span></span>');

        $element->set_control('render_empty', false);

        expect($element->render())->toBe('');
    });

    it('sorts classes and attributes in rendered output', function () {
        $element = new Element('span');
        expect($element->render())->toBe('<span></span>');

        $element->add_class('test');
        expect($element->render())->toBe('<span class="test"></span>');

        $element->add_attribute('hello', 'world');
        expect($element->render())->toBe('<span class="test" hello="world"></span>');

        $element->add_attribute('foo', '');
        expect($element->render())->toBe('<span class="test" foo="" hello="world"></span>');
    });

    it('sorts class names alphabetically for stable output', function () {
        $element = new Element('span');
        $element->add_classes(['zebra', 'alpha', 'mango']);

        expect((string) $element)->toBe('<span class="alpha mango zebra"></span>');
    });

    it('throws when an attribute value is not scalar or stringable', function () {
        $element = new Element('span');
        $element->add_attribute('foo', new stdClass);

        expect(fn () => $element->render())->toThrow(InvalidArgumentException::class);
    });
});

describe('trait composition', function () {
    it('works without extending Component or Element', function () {
        $widget = new class('div') implements ElementInterface
        {
            use ComponentTrait, ElementTrait {
                ElementTrait::render insteadof ComponentTrait;
            }

            public function __construct(public readonly string $tag) {}
        };

        $widget->add_content('hello');

        expect((string) $widget)->toBe('<div>hello</div>');
    });
});
