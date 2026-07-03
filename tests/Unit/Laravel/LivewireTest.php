<?php

declare(strict_types=1);

use Illuminate\Contracts\View\View;
use Illuminate\Support\HtmlString;
use Livewire\Livewire as LivewireFacade;
use Ozzie\Html\Tests\Fixtures\Laravel\DynamicCounter;
use Ozzie\Html\Tests\Fixtures\Laravel\DynamicLivewire;
use Ozzie\Html\Tests\LaravelTestCase;

uses(LaravelTestCase::class);

describe('to_livewire()', function () {
    it('mounts the component alias with constructor params', function () {
        LivewireFacade::shouldReceive('mount')
            ->once()
            ->with(DynamicLivewire::alias(), ['name' => 'Taylor'])
            ->andReturn('<livewire-fragment>');

        $component = new DynamicLivewire(['name' => 'Taylor']);

        expect($component->to_livewire())->toBe('<livewire-fragment>');
    });

    it('casts to the mounted Livewire output', function () {
        LivewireFacade::shouldReceive('mount')
            ->once()
            ->with(DynamicLivewire::alias(), ['name' => 'Taylor'])
            ->andReturn('<livewire-string>');

        $component = new DynamicLivewire(['name' => 'Taylor']);

        expect((string) $component)->toBe('<livewire-string>');
    });
});

describe('escape()', function () {
    it('escapes html', function () {
        $component = new DynamicLivewire();

        expect($component->escaped('Hello <strong>bold</strong> & "quote"'))
            ->toBe('Hello &lt;strong&gt;bold&lt;/strong&gt; &amp; &quot;quote&quot;');
    });
});

describe('render()', function () {
    it('returns a constant blade template for stringable views', function () {
        $component = new DynamicLivewire(provided_view: new HtmlString('<div>Rendered</div>'));

        expect($component->render())->toBe('{!! $this->pull_view() !!}');
    });

    it('builds the view once per render', function () {
        $component = new DynamicLivewire(provided_view: new HtmlString('<div>Rendered</div>'));

        $component->render();

        expect($component->view_calls)->toBe(1)
            ->and((string) $component->pulled_view())->toBe('<div>Rendered</div>')
            ->and($component->view_calls)->toBe(1);
    });

    it('builds fresh when pulled without a prior render', function () {
        $component = new DynamicLivewire(provided_view: new HtmlString('<div>Rendered</div>'));

        expect((string) $component->pulled_view())->toBe('<div>Rendered</div>')
            ->and($component->view_calls)->toBe(1);
    });

    it('returns view instances without casting them', function () {
        $view = Mockery::mock(View::class);
        $component = new DynamicLivewire(provided_view: $view);

        expect($component->render())->toBe($view);
    });
});

describe('Livewire integration', function () {
    it('mounts registered components through the package wrapper', function () {
        $html = (new DynamicCounter())->to_livewire();

        expect($html)
            ->toContain('wire:snapshot')
            ->toContain('wire:click="increment"')
            ->toContain('Count: 0');
    });

    it('updates rendered content after Livewire actions', function () {
        LivewireFacade::test(DynamicCounter::alias())
            ->assertSee('Count: 0')
            ->call('increment')
            ->assertSee('Count: 1')
            ->call('increment')
            ->assertSee('Count: 2');
    });

    it('passes mount parameters through the real Livewire test path', function () {
        LivewireFacade::test(DynamicCounter::alias(), ['label' => 'Clicks'])
            ->assertSee('Clicks: 0')
            ->call('increment')
            ->assertSee('Clicks: 1');
    });

    it('does not recompile view output as blade through the real Livewire render path', function () {
        $counter = LivewireFacade::test(DynamicCounter::alias(), [
            'label' => 'Hello @{{ $name }} <strong>{!! $html !!}</strong>',
        ]);

        $counter
            ->assertSeeHtml('Hello @{{ $name }} &lt;strong&gt;{!! $html !!}&lt;/strong&gt;: 0')
            ->assertDontSee('<strong>', false)
            ->assertDontSee('</strong>', false);
    });
});
