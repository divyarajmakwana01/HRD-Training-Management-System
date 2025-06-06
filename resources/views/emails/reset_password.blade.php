<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
</head>

<body>
    <h1>Password Reset Request</h1>
    <p>You are receiving this email because we received a password reset request for your account.</p>
    <p>Click the button below to reset your password:</p>
    <p>
        <a href="{{ route('participants.reset_password', ['token' => $token]) }}"
            style="display: inline-block; padding: 10px 20px; font-size: 16px; color: #ffffff; background-color: #007bff; border-radius: 5px; text-decoration: none;">Reset
            Password</a>
    </p>
    <p>If you did not request a password reset, no further action is required.</p>
    <p>Thank you,<br>INFLIBNET Centre</p>
</body>

</html>
