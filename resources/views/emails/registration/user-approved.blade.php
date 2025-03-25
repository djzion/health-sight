@component('mail::message')
# Account Approved

Hello {{ $user->name }},

Great news! Your HealthSight account has been approved by our administrators.

You can now log in using your email and password.

@component('mail::button', ['url' => route('login')])
Login Now
@endcomponent

Thank you,<br>
{{ config('app.name') }} Team
@endcomponent
