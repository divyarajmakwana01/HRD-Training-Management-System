<!doctype html>
<html lang="en" data-bs-theme="auto">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Admin Login</title>

    <!-- Stylesheets -->
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/bootstrap-icons.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/darkmode.css') }}" rel="stylesheet">
    <link href="{{ asset('css/sign-in.css') }}" rel="stylesheet">


</head>

<body class="d-flex align-items-center justify-content-center vh-100 bg-light">
    <main class="login-container">
        <form action="{{ route('admin.login.post') }}" method="POST">
            @csrf

            <!-- Logo -->
            <img class="login-logo" src="{{ asset('images/HRDlogo.png') }}" alt="HRD Logo">

            <!-- Title -->
            <h1 class="login-title">Admin Sign In</h1>

            <!-- Email Input -->
            <div class="mb-3 text-start">
                <label for="email" class="form-label"><i class="bi bi-envelope"></i> Email Address</label>
                <input type="email" name="username" class="form-control" id="email" placeholder="name@example.com"
                    required>
            </div>

            <!-- Password Input -->
            <div class="mb-3 text-start">
                <label for="password" class="form-label"><i class="bi bi-key"></i> Password</label>
                <input type="password" name="password" class="form-control" id="password" placeholder="Password"
                    required>
            </div>

            <!-- Captcha Section -->
            <div class="captcha-container mb-3">
                <canvas id="captchaCanvas" width="150" height="50"></canvas>
                <button class="btn btn-sm btn-outline-secondary ms-2" id="refresh-captcha" type="button">
                    <i class="bi bi-arrow-clockwise"></i>
                </button>
            </div>

            <input type="hidden" id="captchaInput" name="captcha_answer">

            <!-- Captcha Input -->
            <div class="mb-3 text-start">
                <label for="userCaptchaInput" class="form-label"><i class="bi bi-shield-lock"></i> Enter Captcha</label>
                <input type="text" class="form-control" id="userCaptchaInput" name="captcha_input"
                    placeholder="Enter Captcha" required>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <!-- Submit Button -->
            <button class="btn btn-primary w-100 py-2" type="submit">
                <i class="bi bi-box-arrow-in-right"></i> Sign in
            </button>

            <!-- Error Message -->
            @if (Session::has('message'))
                <p class="text-danger text-center mt-2">{{ Session::get('message') }}</p>
            @endif

            <!-- Footer -->
            <p class="text-center mt-3 text-muted">&copy; 2025 INFLIBNET Centre, Gandhinagar. All rights reserved.</p>
        </form>
    </main>

    <!-- JavaScript -->
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/captcha.js') }}"></script>
</body>

</html>
