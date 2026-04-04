<x-mail::message>
# New Registration Awaiting Approval

**{{ $pendingUser->name }}** ({{ $pendingUser->email }}) has registered and is waiting for approval.

<x-mail::button :url="route('users.index')">
Review in Users
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
