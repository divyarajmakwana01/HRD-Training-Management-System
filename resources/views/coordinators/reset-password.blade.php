<!doctype html>
<html lang="en" data-bs-theme="auto">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $is_account_creation ? 'Set Password' : 'Reset Password' }}</title>

    <!-- Bootstrap CSS -->
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/bootstrap-icons.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/darkmode.css') }}" rel="stylesheet">
    <link href="{{ asset('css/sign-in.css') }}" rel="stylesheet">

    <style>
        body {
            background-color: #f8f9fa;
        }

        .form-container {
            max-width: 500px;
            width: 100%;
            padding: 30px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .footer-text {
            font-size: 14px;
            text-align: center;
            color: #6c757d;
            margin-top: 10px;
        }
    </style>
</head>

<body class="d-flex align-items-center justify-content-center min-vh-100">

    <div class="form-container">
        <!-- Logo -->
        <img src="{{ asset('images/HRDlogo.png') }}" alt="HRD Logo" class="login-logo">

        <h1 class="h4 mb-3 fw-bold text-center">{{ $is_account_creation ? 'Set Password' : 'Reset Password' }}</h1>
        <p class="text-muted text-center">Enter your new password below.</p>

        <!-- Password Reset Form -->
        <form action="{{ route('coordinator.reset-password', ['token' => $token]) }}" method="POST">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <!-- New Password -->
            <div class="mb-3">
                <label for="password" class="form-label"><i class="bi bi-key"></i> New Password</label>
                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                    id="password" placeholder="Enter new password" required>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Confirm New Password -->
            <div class="mb-3">
                <label for="password_confirmation" class="form-label"><i class="bi bi-lock"></i> Confirm New
                    Password</label>
                <input type="password" name="password_confirmation" class="form-control" id="password_confirmation"
                    placeholder="Confirm new password" required>
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
        </form>

        <!-- Footer -->
        <p class="footer-text">&copy; 2025 INFLIBNET Centre, Gandhinagar. All rights reserved.</p>
    </div>

    <!-- Bootstrap JS -->
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>

</body>

</html>
