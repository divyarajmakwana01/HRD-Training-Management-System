<!DOCTYPE html>
<html>

<head>
    <title>Password Reset</title>
</head>

<body>
    <h2>Password Reset Request</h2>
    <p>Click the link below to reset your password:</p>
    <a href="{{ $resetLink }}">{{ $resetLink }}</a>
    <p>This link will expire in 1 hour.</p>
</body>

</html>
