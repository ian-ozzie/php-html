<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use Illuminate\View\ComponentAttributeBag;
use Illuminate\View\View;
use Ozzie\Html\Laravel\Component;
use Ozzie\Html\Laravel\Element;
use Ozzie\Html\Tests\LaravelTestCase;

uses(LaravelTestCase::class);

describe('Blade component syntax', function () {
    it('renders component slot content with blade component syntax', function () {
        $html = Blade::render('<x-text>Hello</x-text>');

        expect($html)->toBe('Hello');
    });

    it('renders element attributes and slot content with blade component syntax', function () {
        $html = Blade::render('<x-span id="example" class="extra" disabled>Hello</x-span>');

        expect($html)->toBe('<span class="extra" disabled id="example">Hello</span>');
    });

    it('renders class arrays with blade component syntax', function () {
        $html = Blade::render('<x-span @class([\'foo\', \'bar\' => true, \'baz\' => false])>Hello</x-span>');

        expect($html)->toBe('<span class="bar foo">Hello</span>');
    });

    it('renders bound class arrays with blade component syntax', function () {
        $html = Blade::render('<x-span :class="[\'active\' => $active, \'hidden\' => false, \'p-2\']">Hello</x-span>', [
            'active' => true,
        ]);

        expect($html)->toBe('<span class="active p-2">Hello</span>');
    });

    it('sorts blade class attributes with blade component syntax', function () {
        $html = Blade::render('<x-span class="foo bar">Hello</x-span>');

        expect($html)->toBe('<span class="bar foo">Hello</span>');
    });

    it('escapes bound attributes once with blade component syntax', function () {
        $html = Blade::render('<x-span :title="$title">Hello</x-span>', [
            'title' => 'Foo & Bar',
        ]);

        expect($html)->toBe('<span title="Foo &amp; Bar">Hello</span>');
    });

    it('does not escape static (unbound) blade attributes', function () {
        $blade = Blade::render('<x-span title="Foo & Bar">Hello</x-span>');
        expect($blade)->toBe('<span title="Foo & Bar">Hello</span>');
    });

    it('merges constructor classes with blade attributes', function () {
        $html = Blade::render('<x-base-span id="example" class="extra" disabled>Hello</x-base-span>');

        expect($html)->toBe('<span class="base extra" disabled id="example">Hello</span>');
    });

    it('does not mutate the original component instance between renders', function () {
        $first = Blade::render('<x-base-span id="first">Hello</x-base-span>');
        $second = Blade::render('<x-base-span>World</x-base-span>');

        expect($first)->toBe('<span class="base" id="first">Hello</span>');
        expect($second)->toBe('<span class="base">World</span>');
    });

    it('skips null and false bound attributes', function () {
        $html = Blade::render('<x-span :id="$id" :hidden="$hidden">hello</x-span>', [
            'id' => null,
            'hidden' => false,
        ]);

        expect($html)->toBe('<span>hello</span>');
    });
});

describe('__construct()', function () {
    it('applies void tags', function () {
        $element = new class('br') extends Element {};

        expect($element->render())->toBe('<br>');
    });

    it('applies content', function () {
        $element = new class('span', content: 'hello') extends Element {};

        expect($element->render())->toBe('<span>hello</span>');
    });
});

describe('resolveView()', function () {
    it('renders component slot content as an HTML string', function () {
        $component = new class extends Component {};

        $view = $component->resolveView();

        expect($view)->toBeInstanceOf(View::class)
            ->and($view->with(['slot' => 'hello'])->render())
            ->toBe('hello');
    });

    it('renders component slot content directly through the blade bridge', function () {
        $component = new class extends Component {};

        $result = $component->renderBlade(['slot' => 'hello']);

        assert($result instanceof HtmlString);
        expect((string) $result)->toBe('hello');
    });

    it('renders components from a clone', function () {
        $component = new class extends Component {};

        $view = $component->resolveView();
        $result = $view->with(['slot' => 'hello'])->render();

        expect($result)->toBe('hello');
        expect($component->render())->toBe('');
    });

    it('throws when a blade attribute name is not a string', function () {
        $element = new class('span') extends Element {};
        $attributes = new ComponentAttributeBag([true]);

        expect(fn () => $element->renderBlade(['attributes' => $attributes]))
            ->toThrow(InvalidArgumentException::class, 'attribute name (integer) must be a string');
    });

    it('throws when a blade attribute value is not scalar or stringable', function () {
        $element = new class('span') extends Element {};
        $attributes = new ComponentAttributeBag(['value' => new stdClass]);

        expect(fn () => $element->renderBlade(['attributes' => $attributes]))
            ->toThrow(InvalidArgumentException::class, 'attribute "value" value (object) must be scalar or Stringable');
    });
});
