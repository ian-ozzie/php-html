<?php

declare(strict_types=1);

use Ozzie\Html\Element\Html;

describe('__construct()', function () {
    it('creates a complete empty HTML document', function () {
        $element = new Html();

        expect((string) $element)->toBe('<!DOCTYPE html><html><body></body></html>');
    });
});

describe('add_content()', function () {
    it('appends content to the body element', function () {
        $element = new Html();
        $element->add_content('Hello World');

        expect((string) $element)->toBe('<!DOCTYPE html><html><body>Hello World</body></html>');
    });
});

describe('prepend_content()', function () {
    it('prepends content before existing body content', function () {
        $element = new Html();
        $element->add_content('Hello World!');
        $element->prepend_content('!');

        expect((string) $element)->toBe('<!DOCTYPE html><html><body>!Hello World!</body></html>');
    });
});

describe('set_content()', function () {
    it('replaces the body content', function () {
        $element = new Html();
        $element->add_content('Hello World');
        $element->set_content('foo');

        expect((string) $element)->toBe('<!DOCTYPE html><html><body>foo</body></html>');
    });
});

describe('add_element()', function () {
    it('adds child elements inside the body', function () {
        $element = new Html();
        $result = $element->add_element('span');

        expect($result)->toBe($element);
        expect((string) $element)->toBe('<!DOCTYPE html><html><body><span></span></body></html>');
    });
});

describe('new_element()', function () {
    it('returns the new body child element', function () {
        $element = new Html();
        $result = $element->new_element('span');

        expect($result)->not->toBe($element);
        expect((string) $element)->toBe('<!DOCTYPE html><html><body><span></span></body></html>');
    });
});

describe('render()', function () {
    it('includes the head when head content exists', function () {
        $element = new Html();
        $element->head->add_content('<meta charset="utf-8">');

        expect((string) $element)->toBe('<!DOCTYPE html><html><head><meta charset="utf-8"></head><body></body></html>');
    });

    it('includes the title inside the head when populated', function () {
        $element = new Html();
        $element->title->add_content('My Page');

        expect((string) $element)->toBe('<!DOCTYPE html><html><head><title>My Page</title></head><body></body></html>');
    });

    it('includes noscript inside the body when populated', function () {
        $element = new Html();
        $element->noscript->add_content('Please enable JavaScript.');

        expect((string) $element)->toBe('<!DOCTYPE html><html><body><noscript>Please enable JavaScript.</noscript></body></html>');
    });

    it('uses a custom doctype when one is configured', function () {
        $element = new Html();
        $element->doctype = 'html PUBLIC "-//W3C//DTD HTML 4.01//EN"';

        expect((string) $element)->toStartWith('<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN">');
    });
});
