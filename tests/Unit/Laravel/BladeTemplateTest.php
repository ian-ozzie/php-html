<?php

declare(strict_types=1);

use Ozzie\Html\Element;
use Ozzie\Html\Laravel\BladeTemplate;
use Ozzie\Html\Tests\LaravelTestCase;

uses(LaravelTestCase::class);

describe('render()', function () {
    it('compiles the template with data', function () {
        $template = new BladeTemplate('Hello {{ $name }}', ['name' => 'Taylor']);

        expect($template->render())->toBe('Hello Taylor');
    });

    it('escapes data through blade echo syntax', function () {
        $template = new BladeTemplate('{{ $value }}', ['value' => '<strong>bold</strong>']);

        expect($template->render())->toBe('&lt;strong&gt;bold&lt;/strong&gt;');
    });

    it('compiles directives', function () {
        $template = new BladeTemplate('@if($show) shown @else hidden @endif', ['show' => true]);

        expect(trim($template->render()))->toBe('shown');
    });

    it('casts to the compiled output', function () {
        $template = new BladeTemplate('Hello {{ $name }}', ['name' => 'Taylor']);

        expect((string) $template)->toBe('Hello Taylor');
    });
});

describe('element content', function () {
    it('renders as content within an element tree', function () {
        $element = new Element('div', [], new BladeTemplate('Hello {{ $name }}', ['name' => 'Taylor']));

        expect($element->render())->toBe('<div>Hello Taylor</div>');
    });
});
