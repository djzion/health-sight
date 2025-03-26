<!-- resources/views/emails/registration/user-rejected.blade.php -->
@component('mail::message')
# Registration Status Update

Dear {{ $user->full_name }},

Thank you for your interest in HealthSight.

We regret to inform you that your registration request could not be approved at this time.

If you believe this is an error or would like more information, please contact our support team.

Thank you,<br>
{{ config('app.name') }} Team
@endcomponent
