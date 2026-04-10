<?php

declare(strict_types=1);

use App\Livewire\Blueprints\Components\FieldCard;
use App\Models\Field;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $user = User::factory()->admin()->create();
    actingAs($user);
});

test('duplicate creates a copy of the field with _copy suffix', function () {
    $field = Field::factory()->create([
        'type' => 'text',
        'label' => 'Post Title',
        'handle' => 'post_title',
        'order' => 1,
    ]);

    Livewire::test(FieldCard::class, ['fieldId' => $field->id, 'field' => $field])
        ->call('duplicate')
        ->assertDispatched('field-added');

    $copy = Field::where('handle', 'post_title_copy')->first();

    expect($copy)->not->toBeNull()
        ->and($copy->label)->toBe('Post Title (Copy)')
        ->and($copy->blueprint_id)->toBe($field->blueprint_id)
        ->and($copy->type)->toBe($field->type);
});

test('duplicate preserves the original field', function () {
    $field = Field::factory()->create(['type' => 'text', 'handle' => 'my_field', 'order' => 1]);

    Livewire::test(FieldCard::class, ['fieldId' => $field->id, 'field' => $field])
        ->call('duplicate');

    expect(Field::find($field->id))->not->toBeNull();
    expect(Field::where('handle', 'my_field')->count())->toBe(1);
    expect(Field::where('handle', 'my_field_copy')->count())->toBe(1);
});
