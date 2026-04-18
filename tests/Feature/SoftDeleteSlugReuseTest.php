<?php

declare(strict_types=1);

use App\Models\Blueprint;
use App\Models\Collection;
use App\Models\Entry;
use App\Models\User;

use function Pest\Laravel\actingAs;

beforeEach(function (): void {
    actingAs(User::factory()->admin()->create());
});

describe('Blueprint slug reuse after soft delete', function (): void {
    test('mangled slug on soft delete', function (): void {
        $blueprint = Blueprint::factory()->create(['slug' => 'blog-post']);

        $blueprint->delete();

        expect($blueprint->fresh()->slug)->toBe('blog-post__deleted_' . $blueprint->id);
    });

    test('original slug is available after deletion', function (): void {
        $blueprint = Blueprint::factory()->create(['slug' => 'blog-post']);
        $blueprint->delete();

        $newBlueprint = Blueprint::factory()->create(['slug' => 'blog-post']);

        expect($newBlueprint->slug)->toBe('blog-post');
    });
});

describe('Collection slug reuse after soft delete', function (): void {
    test('mangles slug on soft delete', function (): void {
        $collection = Collection::factory()->create(['slug' => 'blog-posts']);

        $collection->delete();

        expect($collection->fresh()->slug)->toBe('blog-posts__deleted_' . $collection->id);
    });

    test('original slug is available after deletion', function (): void {
        $collection = Collection::factory()->create(['slug' => 'blog-posts']);
        $collection->delete();

        $newCollection = Collection::factory()->create(['slug' => 'blog-posts']);

        expect($newCollection->slug)->toBe('blog-posts');
    });
});

describe('Entry slug reuse after soft delete', function (): void {
    test('mangles slug on soft delete', function (): void {
        $entry = Entry::factory()->create(['slug' => 'my-first-post']);

        $entry->delete();

        expect($entry->fresh()->slug)->toBe('my-first-post__deleted_' . $entry->id);
    });

    test('original slug is available after deletion', function (): void {
        $entry = Entry::factory()->create(['slug' => 'my-first-post']);
        $entry->delete();

        $newEntry = Entry::factory()->create(['slug' => 'my-first-post']);

        expect($newEntry->slug)->toBe('my-first-post');
    });
});
