<!doctype html>
<html lang="en" data-bs-theme="auto">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Forgot Password</title>

    <!-- Bootstrap & Custom Styles -->
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/bootstrap-icons.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/darkmode.css') }}" rel="stylesheet">
    <link href="{{ asset('css/sign-in.css') }}" rel="stylesheet">


</head>

<body class="d-flex align-items-center justify-content-center vh-100 bg-light">
    <div class="login-container">
        <!-- Logo -->
        <img src="{{ asset('images/HRDlogo.png') }}" alt="HRD Logo" class="login-logo">

        <!-- Title -->
        <h1 class="login-title">Forgot Password?</h1>
        <p class="text-muted">Enter your registered email, and we’ll send you a password reset link.</p>

        <!-- Password Reset Form -->
        <form action="{{ route('participants.reset_password_request.post') }}" method="POST">
            @csrf

            <!-- Email Input -->
            <div class="form-floating mb-3">
                <input type="email" name="email" class="form-control" id="floatingInput"
                    placeholder="name@example.com" required>
                <label for="floatingInput"><i class="bi bi-envelope"></i> Email Address</label>
            </div>

            <!-- Submit Button -->
            <button class="btn btn-primary w-100 py-2" type="submit">
                <i class="bi bi-send"></i> Send Password Reset Link
            </button>

            <!-- Back to Login Button -->
            <a href="{{ route('participants.login') }}" class="btn btn-secondary w-100 py-2 mt-3">
                <i class="bi bi-arrow-left"></i> Back to Login
            </a>

            <!-- Display Validation Errors -->
            @if ($errors->any())
                <div class="alert alert-danger mt-3">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Success Message -->
            @if (Session::has('message'))
                <div class="alert alert-success mt-3">{{ Session::get('message') }}</div>
            @endif

            <!-- Footer -->
            <p class="footer-links mt-4">&copy; 2025 INFLIBNET Centre, Gandhinagar. All rights reserved.</p>
        </form>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
</body>

</html>
