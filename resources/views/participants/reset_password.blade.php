<!doctype html>
<html lang="en" data-bs-theme="auto">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $is_account_creation ? 'Set Password' : 'Reset Password' }}</title>

    <!-- Stylesheets -->
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
        <h1 class="login-title">{{ $is_account_creation ? 'Set Password' : 'Reset Password' }}</h1>
        <p class="text-muted">Enter your new password below.</p>

        <!-- Password Reset Form -->
        <form action="{{ route('participants.reset_password.post') }}" method="POST">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <!-- New Password -->
            <div class="form-floating mb-3">
                <input type="password" name="password" class="form-control" id="floatingPassword"
                    placeholder="New Password" required>
                <label for="floatingPassword"><i class="bi bi-key"></i> New Password</label>
            </div>

            <!-- Confirm New Password -->
            <div class="form-floating mb-3">
                <input type="password" name="password_confirmation" class="form-control"
                    id="floatingPasswordConfirmation" placeholder="Confirm New Password" required>
                <label for="floatingPasswordConfirmation"><i class="bi bi-lock"></i> Confirm New Password</label>
            </div>

            <!-- Submit Button -->
            <button class="btn btn-primary w-100 py-2" type="submit">
                <i class="bi bi-check-circle"></i> {{ $is_account_creation ? 'Set Password' : 'Reset Password' }}
            </button>

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
