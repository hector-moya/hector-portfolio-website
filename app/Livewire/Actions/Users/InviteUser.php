<?php

declare(strict_types=1);

namespace App\Livewire\Actions\Users;

use App\Mail\InvitationMail;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

final class InviteUser
{
    public function invite(string $email, string $role): Invitation
    {
        Gate::authorize('create', User::class);

        $invitation = Invitation::query()->create([
            'email' => $email,
            'role' => $role,
            'token' => Str::random(64),
            'invited_by' => auth()->id(),
            'expires_at' => now()->addHours(48),
        ]);

        Mail::to($email)->queue(new InvitationMail($invitation));

        return $invitation;
    }
}
