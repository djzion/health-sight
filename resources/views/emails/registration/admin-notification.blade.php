<div style="font-family: Arial, sans-serif; padding: 20px; max-width: 600px; margin: 0 auto;">
    <h2 style="color: #333;">New User Registration</h2>

    <p>A new user has registered and requires your approval:</p>

    <div style="background-color: #f5f5f5; padding: 15px; border-radius: 5px; margin: 20px 0;">
        <p><strong>Name:</strong> {{ $userName }}</p>
        <p><strong>Email:</strong> {{ $userEmail }}</p>
    </div>

    <div style="margin: 30px 0;">
        <a href="{{ $pendingUsersUrl }}" style="background-color: #4a90e2; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">
            Review Pending Users
        </a>
    </div>

    <p>Thank you,<br>HealthSight System</p>
</div>
