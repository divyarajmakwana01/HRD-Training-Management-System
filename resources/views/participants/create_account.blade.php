<!doctype html>
<html lang="en" data-bs-theme="auto">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Participant Registration</title>

    <!-- Stylesheets -->
    <link href="{{ url('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ url('css/bootstrap-icons.min.css') }}" rel="stylesheet">
    <link href="{{ url('css/sign-in.css') }}" rel="stylesheet">


</head>

<body class="d-flex align-items-center justify-content-center vh-100 bg-light">
    <div class="login-container">
        <!-- Logo -->
        <img src="{{ asset('images/HRDlogo.png') }}" alt="HRD Logo" class="login-logo">

        <!-- Title -->
        <h1 class="login-title">Please Register</h1>

        <form action="{{ route('participants.create_account.post') }}" method="POST">
            @csrf

            <!-- Email Input -->
            <div class="form-floating mb-3">
                <input type="email" class="form-control" id="floatingInput" name="email"
                    placeholder="name@example.com" required>
                <label for="floatingInput"><i class="bi bi-envelope"></i> Email Address</label>
            </div>

            <!-- Captcha Section -->
            <div class="captcha-container mb-3">
                <canvas id="captchaCanvas" width="150" height="50"></canvas>
                <button class="btn btn-sm btn-outline-secondary ms-2" id="refresh-captcha" type="button">
                    <i class="bi bi-arrow-clockwise"></i>
                </button>
            </div>

            <input type="hidden" id="captchaInput" name="captcha">

            <!-- Captcha Input -->
            <div class="form-floating mb-3">
                <input type="text" class="form-control" id="userCaptchaInput" name="captcha_input"
                    placeholder="Enter Captcha" required>
                <label for="userCaptchaInput"><i class="bi bi-shield-lock"></i> Enter Captcha</label>
            </div>

            <!-- Submit Button -->
            <button class="btn btn-primary w-100 py-2" type="submit">
                <i class="bi bi-send"></i> Send Registration Link
            </button>

            <!-- Back to Login Button -->
            <a href="{{ route('participants.login') }}" class="btn btn-secondary w-100 py-2 mt-3">
                <i class="bi bi-arrow-left"></i> Back to Login
            </a>

            <!-- Footer -->
            <p class="footer-links mt-4">&copy; 2025 INFLIBNET Centre, Gandhinagar. All rights reserved.</p>
        </form>
    </div>

    <!-- JavaScript -->
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/captcha.js') }}"></script>
</body>

</html>
