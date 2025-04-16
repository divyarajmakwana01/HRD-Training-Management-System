<!DOCTYPE html>
<html>

<head>
    <title>Activation Link</title>
</head>

<body>
    <p>Click the link below to activate your account:</p>
    <a href="{{ route('participants.reset_password', ['token' => $token]) }}">
        {{ route('participants.reset_password', ['token' => $token]) }}
    </a>
</body>

</html>
