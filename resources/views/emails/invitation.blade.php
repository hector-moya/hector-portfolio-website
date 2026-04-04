<x-mail::message>
# You've been invited to {{ config('app.name') }}

You have been invited to create an account as a **{{ $invitation->role }}**.

This invitation link expires in 48 hours.

<x-mail::button :url="route('register', ['token' => $invitation->token])">
Accept Invitation
</x-mail::button>

If you did not expect this invitation, you can safely ignore this email.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
