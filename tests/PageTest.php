<?php

declare(strict_types=1);

use Ozzie\Html\Page;

test('construct', function () {
    $reflector = new ReflectionClass(Page::class);
    $constructor = $reflector->getConstructor();
    expect($constructor)->not->toBeNull();
    expect($constructor?->isPrivate())->toBeTrue();
});

test('get_instance', function () {
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
