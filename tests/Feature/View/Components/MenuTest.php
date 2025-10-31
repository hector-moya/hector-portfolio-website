<?php

namespace Tests\Feature\View\Components;

use App\Models\Navigation;
use App\Models\NavigationItem;

test('menu component can render navigation', function () {
    $navigation = Navigation::factory()->create([
        'name' => 'Main Menu',
        'handle' => 'main-menu',
    ]);

    $items = NavigationItem::factory()->count(2)->create([
        'navigation_id' => $navigation->id,
        'parent_id' => null,
    ]);

    $child = NavigationItem::factory()->create([
        'navigation_id' => $navigation->id,
        'parent_id' => $items->first()->id,
    ]);

    $view = $this->blade(
        '<x-menu handle="main-menu" />'
    );

    $view->assertSee($items->first()->title)
        ->assertSee($items->last()->title)
        ->assertSee($child->title);
});

test('menu component handles empty navigation', function () {
    $view = $this->blade(
        '<x-menu handle="non-existent" />'
    );

    $view->assertDontSee('class="flex gap-8"');
});
