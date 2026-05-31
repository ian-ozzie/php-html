<?php

declare(strict_types=1);

use Ozzie\Html\Page;

afterEach(function () {
    Page::reset();
});

describe('__construct()', function () {
    it('is private so pages are created through the registry', function () {
        $reflector = new ReflectionClass(Page::class);
        $constructor = $reflector->getConstructor();

        expect($constructor)->not->toBeNull();
        expect($constructor?->isPrivate())->toBeTrue();
    });
});

describe('get_instance()', function () {
    it('returns one shared page instance per path', function () {
        $root = Page::get_instance('/');
        expect($root)->toBeInstanceOf(Page::class);

        $foo = Page::get_instance('/foo');
        expect($foo)->toBeInstanceOf(Page::class);
        expect($foo)->not->toBe($root);

        $duplicate = Page::get_instance('/');
        expect($duplicate)->toBeInstanceOf(Page::class);
        expect($duplicate)->toBe($root);

        $bar = Page::get_instance('/foo');
        expect($bar)->toBeInstanceOf(Page::class);
        expect($bar)->toBe($foo);
    });
});
