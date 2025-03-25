<div style="font-family: Arial, sans-serif; padding: 20px; max-width: 600px; margin: 0 auto;">
    <h2 style="color: #333;">Registration Pending Approval</h2>

    <p>Hello {{ $userName }},</p>

    <p>Thank you for registering with HealthSight. Your account is currently pending administrator approval.</p>

    <p>We'll notify you at {{ $userEmail }} once your account has been reviewed and approved.</p>

    <div style="margin: 30px 0;">
        <a href="{{ config('app.url') }}" style="background-color: #4a90e2; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">
            Return to HealthSight
        </a>
    </div>

    <p>Thank you,<br>HealthSight Team</p>
</div>
