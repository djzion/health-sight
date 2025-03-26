// resources/views/emails/plain/admin-notification.blade.php
<!DOCTYPE html>
<html>
<head>
    <title>New Registration</title>
</head>
<body>
    <h2>New User Registration</h2>
    <p>A new user has registered and requires your approval:</p>
    <p><strong>Name:</strong> {{ $userData['userName'] }}</p>
    <p><strong>Email:</strong> {{ $userData['userEmail'] }}</p>
    <p>Please review this registration in the admin dashboard.</p>
    <p>Thank you,<br>HealthSight System</p>
</body>
</html>
