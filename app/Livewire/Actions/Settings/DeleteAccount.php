<?php

declare(strict_types=1);

namespace App\Livewire\Actions\Settings;

use App\Livewire\Actions\Logout;
use App\Models\User;

final readonly class DeleteAccount
{
    public function __construct(private Logout $logout) {}

    public function delete(User $user): void
    {
        tap($user, ($this->logout)(...));

        $user->delete();
    }
}
