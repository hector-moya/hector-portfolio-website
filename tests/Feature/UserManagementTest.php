<?php

use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;

beforeEach(function () {
    $this->admin = User::factory()->admin()->create();
    $this->editor = User::factory()->editor()->create();
    $this->viewer = User::factory()->viewer()->create();
});

// Authorization Tests
test('admin can view users index', function () {
    actingAs($this->admin)
        ->get(route('users.index'))
        ->assertSuccessful();
});

test('editor cannot view users index', function () {
    actingAs($this->editor)
        ->get(route('users.index'))
        ->assertForbidden();
});

test('viewer cannot view users index', function () {
    actingAs($this->viewer)
        ->get(route('users.index'))
        ->assertForbidden();
});

test('admin can view create user page', function () {
    actingAs($this->admin)
        ->get(route('users.create'))
        ->assertSuccessful();
});

test('editor cannot view create user page', function () {
    actingAs($this->editor)
        ->get(route('users.create'))
        ->assertForbidden();
});

test('users index shows all users', function () {
    Livewire::actingAs($this->admin)
        ->test(\App\Livewire\Users\Index::class)
        ->assertSee($this->admin->name)
        ->assertSee($this->admin->email)
        ->assertSee($this->editor->name)
        ->assertSee($this->viewer->name);
});

test('users index can search by name', function () {
    $user1 = User::factory()->create(['name' => 'John Doe']);
    $user2 = User::factory()->create(['name' => 'Jane Smith']);

    Livewire::actingAs($this->admin)
        ->test(\App\Livewire\Users\Index::class)
        ->set('search', 'John')
        ->assertSee('John Doe')
        ->assertDontSee('Jane Smith');
});

test('users index can filter by role', function () {
    Livewire::actingAs($this->admin)
        ->test(\App\Livewire\Users\Index::class)
        ->set('roleFilter', 'admin')
        ->assertSee($this->admin->name)
        ->assertDontSee($this->editor->name)
        ->assertDontSee($this->viewer->name);
});

// User Creation Tests
test('admin can create a new user', function () {
    Livewire::actingAs($this->admin)
        ->test(\App\Livewire\Users\Create::class)
        ->set('form.name', 'New User')
        ->set('form.email', 'newuser@example.com')
        ->set('form.password', 'password123')
        ->set('form.password_confirmation', 'password123')
        ->set('form.role', 'editor')
        ->call('save')
        ->assertRedirect(route('users.index'));

    assertDatabaseHas('users', [
        'name' => 'New User',
        'email' => 'newuser@example.com',
        'role' => 'editor',
    ]);
});

test('user name is required', function () {
    Livewire::actingAs($this->admin)
        ->test(\App\Livewire\Users\Create::class)
        ->set('form.name', '')
        ->set('form.email', 'test@example.com')
        ->set('form.password', 'password123')
        ->set('form.password_confirmation', 'password123')
        ->set('form.role', 'viewer')
        ->call('save')
        ->assertHasErrors(['form.name']);
});

test('user email is required and must be valid', function () {
    Livewire::actingAs($this->admin)
        ->test(\App\Livewire\Users\Create::class)
        ->set('form.name', 'Test User')
        ->set('form.email', '')
        ->set('form.password', 'password123')
        ->set('form.password_confirmation', 'password123')
        ->set('form.role', 'viewer')
        ->call('save')
        ->assertHasErrors(['form.email']);

    Livewire::actingAs($this->admin)
        ->test(\App\Livewire\Users\Create::class)
        ->set('form.name', 'Test User')
        ->set('form.email', 'invalid-email')
        ->set('form.password', 'password123')
        ->set('form.password_confirmation', 'password123')
        ->set('form.role', 'viewer')
        ->call('save')
        ->assertHasErrors(['form.email']);
});

test('user email must be unique', function () {
    Livewire::actingAs($this->admin)
        ->test(\App\Livewire\Users\Create::class)
        ->set('form.name', 'Test User')
        ->set('form.email', $this->admin->email)
        ->set('form.password', 'password123')
        ->set('form.password_confirmation', 'password123')
        ->set('form.role', 'viewer')
        ->call('save')
        ->assertHasErrors(['form.email']);
});

test('user password is required with minimum length', function () {
    Livewire::actingAs($this->admin)
        ->test(\App\Livewire\Users\Create::class)
        ->set('form.name', 'Test User')
        ->set('form.email', 'test@example.com')
        ->set('form.password', 'short')
        ->set('form.password_confirmation', 'short')
        ->set('form.role', 'viewer')
        ->call('save')
        ->assertHasErrors(['form.password']);
});

test('user password must be confirmed', function () {
    Livewire::actingAs($this->admin)
        ->test(\App\Livewire\Users\Create::class)
        ->set('form.name', 'Test User')
        ->set('form.email', 'test@example.com')
        ->set('form.password', 'password123')
        ->set('form.password_confirmation', 'different')
        ->set('form.role', 'viewer')
        ->call('save')
        ->assertHasErrors(['form.password']);
});

test('user role must be valid', function () {
    Livewire::actingAs($this->admin)
        ->test(\App\Livewire\Users\Create::class)
        ->set('form.name', 'Test User')
        ->set('form.email', 'test@example.com')
        ->set('form.password', 'password123')
        ->set('form.password_confirmation', 'password123')
        ->set('form.role', 'invalid-role')
        ->call('save')
        ->assertHasErrors(['form.role']);
});

// User Update Tests
test('admin can edit a user', function () {
    actingAs($this->admin)
        ->get(route('users.edit', $this->editor))
        ->assertSuccessful();
});

test('user can edit themselves', function () {
    actingAs($this->editor)
        ->get(route('users.edit', $this->editor))
        ->assertSuccessful();
});

test('user cannot edit other users', function () {
    actingAs($this->editor)
        ->get(route('users.edit', $this->viewer))
        ->assertForbidden();
});

test('admin can update a user', function () {
    Livewire::actingAs($this->admin)
        ->test(\App\Livewire\Users\Edit::class, ['user' => $this->editor])
        ->set('form.name', 'Updated Name')
        ->set('form.email', 'updated@example.com')
        ->set('form.role', 'admin')
        ->call('save')
        ->assertRedirect(route('users.index'));

    assertDatabaseHas('users', [
        'id' => $this->editor->id,
        'name' => 'Updated Name',
        'email' => 'updated@example.com',
        'role' => 'admin',
    ]);
});

test('user can update themselves', function () {
    Livewire::actingAs($this->editor)
        ->test(\App\Livewire\Users\Edit::class, ['user' => $this->editor])
        ->set('form.name', 'My Updated Name')
        ->call('save')
        ->assertRedirect(route('users.index'));

    assertDatabaseHas('users', [
        'id' => $this->editor->id,
        'name' => 'My Updated Name',
    ]);
});

test('password is optional when updating user', function () {
    $originalPassword = $this->editor->password;

    Livewire::actingAs($this->admin)
        ->test(\App\Livewire\Users\Edit::class, ['user' => $this->editor])
        ->set('form.name', 'Updated Name')
        ->set('form.password', '')
        ->set('form.password_confirmation', '')
        ->call('save')
        ->assertRedirect(route('users.index'));

    $this->editor->refresh();
    expect($this->editor->password)->toBe($originalPassword);
});

test('password is updated when provided', function () {
    $originalPassword = $this->editor->password;

    Livewire::actingAs($this->admin)
        ->test(\App\Livewire\Users\Edit::class, ['user' => $this->editor])
        ->set('form.name', 'Updated Name')
        ->set('form.password', 'newpassword123')
        ->set('form.password_confirmation', 'newpassword123')
        ->call('save')
        ->assertRedirect(route('users.index'));

    $this->editor->refresh();
    expect($this->editor->password)->not->toBe($originalPassword);
});

// User Deletion Tests
test('admin can delete a user', function () {
    $userToDelete = User::factory()->create();

    Livewire::actingAs($this->admin)
        ->test(\App\Livewire\Users\Index::class)
        ->call('delete', $userToDelete->id);

    expect(User::find($userToDelete->id))->toBeNull();
});

test('admin cannot delete themselves', function () {
    Livewire::actingAs($this->admin)
        ->test(\App\Livewire\Users\Index::class)
        ->call('delete', $this->admin->id);

    expect(User::find($this->admin->id))->not->toBeNull();
});

test('editor cannot access users management at all', function () {
    // Editors cannot view users index, so they cannot delete either
    actingAs($this->editor)
        ->get(route('users.index'))
        ->assertForbidden();

    // Verify the policy explicitly
    expect($this->editor->can('delete', User::factory()->make()))->toBeFalse();
});
