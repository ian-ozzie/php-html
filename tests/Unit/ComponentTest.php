<?php

declare(strict_types=1);

use Ozzie\Html\Component;
use Ozzie\Html\Element;

describe('render()', function () {
    it('renders an empty string by default', function () {
        $component = new Component;

        expect($component->render())->toBe('');
    });

    it('casts to its rendered output', function () {
        $component = new Component;

        expect((string) $component)->toBe('');
    });

    it('renders null content as an empty string', function () {
        $component = new Component;
        $component->set_content(null);

        expect($component->render())->toBe('');
    });

    it('renders string content unchanged', function () {
        $component = new Component;
        $component->set_content('foo');

        expect($component->render())->toBe('foo');
    });

    it('renders integer content as a string', function () {
        $component = new Component;
        $component->set_content(42);

        expect($component->render())->toBe('42');
    });

    it('renders float content as a string', function () {
        $component = new Component;
        $component->set_content(3.14);

        expect($component->render())->toBe('3.14');
    });

    it('renders array content in order', function () {
        $component = new Component;
        $component->set_content(['foo', 'bar', 'baz']);

        expect($component->render())->toBe('foobarbaz');
    });

    it('renders stringable objects', function () {
        $component = new Component;
        $stringable = new class implements Stringable
        {
            public function __toString(): string
            {
                return 'foo';
            }
        };

        $component->set_content($stringable);

        expect($component->render())->toBe('foo');
    });

    it('throws when object content is not stringable', function () {
        $component = new Component;
        $component->set_content(new stdClass);

        expect(fn () => $component->render())->toThrow(InvalidArgumentException::class);
    });

    it('throws when content type is unsupported', function () {
        $component = new Component;
        $component->set_content(true);

        expect(fn () => $component->render())->toThrow(InvalidArgumentException::class);
    });
});

describe('add_content()', function () {
    it('appends content to the render stack', function () {
        $component = new Component;
        $component
            ->add_content('foo')
            ->add_content('bar');

        expect((string) $component)->toBe('foobar');
    });

    it('returns the component for chaining', function () {
        $component = new Component;

        expect($component->add_content('foo'))->toBe($component);
    });
});

describe('prepend_content()', function () {
    it('adds content before existing content', function () {
        $component = new Component;
        $component
            ->add_content('foo')
            ->prepend_content('bar');

        expect((string) $component)->toBe('barfoo');
    });

    it('returns the component for chaining', function () {
        $component = new Component;

        expect($component->prepend_content('foo'))->toBe($component);
    });
});

describe('set_content()', function () {
    it('replaces previously added content', function () {
        $component = new Component;
        $component
            ->add_content('foo')
            ->set_content('bar');

        expect((string) $component)->toBe('bar');
    });

    it('normalises array content without adding separators', function () {
        $component = new Component;
        $component->set_content(['foo', 'bar', 'baz']);

        expect((string) $component)->toBe('foobarbaz');
    });

    it('returns the component for chaining', function () {
        $component = new Component;

        expect($component->set_content('foo'))->toBe($component);
    });
});

describe('element()', function () {
    it('creates a plain element instance', function () {
        $element = Component::element('foo');

        expect($element)->toBeInstanceOf(Element::class);
    });
});

describe('add_element()', function () {
    it('appends a new element to the component content', function () {
        $component = new Component;
        $result = $component->add_element('foo');

        expect($result)->toBe($component);
        expect((string) $component)->toBe((string) new Element('foo'));
    });
});

describe('new_element()', function () {
    it('returns the new element after adding it to the component', function () {
        $component = new Component;
        $result = $component->new_element('foo');

        expect($result)->toBeInstanceOf(Element::class);
        expect((string) $component)->toBe((string) new Element('foo'));
    });
});

describe('cache_render', function () {
    it('is disabled by default', function () {
        $component = new Component;

        expect($component->cache_render)->toBeFalse();
    });

    it('returns fresh output when disabled', function () {
        $component = new Component;
        $component->add_content('foo');
        expect($component->render())->toBe('foo');

        $component->add_content('bar');
        expect($component->render())->toBe('foobar');
    });

    it('reuses the first rendered output when enabled', function () {
        $component = new Component;
        $component->cache_render = true;
        $component->add_content('foo');
        expect($component->render())->toBe('foo');

        $component->add_content('bar');
        expect($component->render())->toBe('foo');
    });

    it('can render the same cached child instance multiple times', function () {
        $component = new Component;
        $component->cache_render = true;
        $component->add_content('hello');

        $parent = new Component;
        $parent->add_content($component);
        $parent->add_content($component);

        expect($parent->render())->toBe('hellohello');
    });

    it('prevents render-time composition from duplicating content', function () {
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

    it('allows render-time composition to duplicate content when disabled', function () {
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

    it('keeps an empty first render as the cached value', function () {
        $component = new Component;
        $component->cache_render = true;
        expect($component->render())->toBe('');

        $component->add_content('foo');
        expect($component->render())->toBe('');
    });

    it('ignores content mutation after the cache is warmed', function () {
        $component = new Component;
        $component->cache_render = true;
        $component->add_content('foo');
        $component->render();

        $component->set_content('bar');

        expect($component->render())->toBe('foo');
    });

    it('returns live content again when disabled after the cache is warmed', function () {
        $component = new Component;
        $component->cache_render = true;
        $component->add_content('foo');
        $component->render();

        $component->cache_render = false;
        $component->add_content('bar');

        expect($component->render())->toBe('foobar');
    });
});
