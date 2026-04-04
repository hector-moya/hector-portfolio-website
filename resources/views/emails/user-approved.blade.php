<x-mail::message>
# Your account has been approved!

Hi {{ $user->name }},

Great news! Your registration on **{{ config('app.name') }}** has been approved. You can now log in and access your account.

<x-mail::button :url="route('login')">
Log In Now
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
