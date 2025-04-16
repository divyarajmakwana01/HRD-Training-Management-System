<!DOCTYPE html>
<html lang="en" data-bs-theme="light"> <!-- Default to light mode -->

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Welcome | HRD Training & Conference</title>

    <!-- Load Bootstrap CSS -->
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">

    <style>
        /* Global Styles */
        body {
            transition: background 0.3s, color 0.3s;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background-color: #f0f2f5;
            /* Light background similar to HRD design */
            font-family: 'Roboto', sans-serif;
        }

        .header-banner h1 {
            color: #ffffff;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.6);
            font-size: 2.5rem;
        }

        /* HRD Header Section using header_banner.png */
        header.hrd-header {
            background: url('{{ asset('images/header_banner.png') }}') no-repeat center center;
            background-size: cover;
            position: relative;
            padding: 20px 0;
        }

        header.hrd-header .hrd-logo img {
            max-width: 100%;
            height: auto;
        }

        /* Navbar Styles */
        .navbar {
            background-color: #003366;
            /* Deep blue for a formal look */
        }

        .navbar-brand,
        .navbar-nav .nav-link {
            color: #ffffff !important;
            font-weight: bold;
        }

        .navbar-toggler {
            border-color: #ffffff;
        }

        .navbar-toggler-icon {
            filter: invert(1);
        }

        /* Content Wrapper */
        .content-wrapper {
            flex: 1;
            padding: 20px;
        }

        .content-wrapper h1 {
            color: #003366;
        }

        /* Button Styles */
        .btn-primary {
            background-color: #003366;
            border-color: #003366;
        }

        .btn-primary:hover {
            background-color: #002244;
            border-color: #001d33;
        }

        /* Footer Styles */
        footer {
            background-color: #003366;
            color: #ffffff;
            padding: 15px;
            text-align: center;
            width: 100%;
        }

        /* Dark Mode Styles */
        [data-bs-theme="dark"] body {
            background-color: #121212;
            color: #f8f9fa;
        }

        [data-bs-theme="dark"] .navbar {
            background-color: #1e1e1e !important;
        }

        [data-bs-theme="dark"] .navbar-brand,
        [data-bs-theme="dark"] .navbar-nav .nav-link {
            color: #f8f9fa !important;
        }

        [data-bs-theme="dark"] .btn-outline-light {
            color: #f8f9fa !important;
            border-color: #f8f9fa;
        }

        [data-bs-theme="dark"] .btn-outline-light:hover {
            background-color: #f8f9fa;
            color: #121212 !important;
        }

        [data-bs-theme="dark"] footer {
            background-color: #1e1e1e;
            color: #f8f9fa;
        }
    </style>
</head>

<body>

    <!-- HRD Header Section -->
    <header class="hrd-header">
        <div class="container">
            <div class="row align-items-center">
                <!-- HRD Logo -->
                <div class="col-lg-3 col-md-3 col-sm-4 col-12 logo">
                    <ul class="hrd-logo list-unstyled">
                        <li>
                            <img class="img-responsive" src="{{ asset('images/HRDlogo.png') }}" alt="HRD Logo">
                            </a>
                        </li>
                    </ul>
                </div>
                <!-- Spacer for layout -->
                <div class="col-lg-1 col-md-1 d-none d-md-block"></div>
                <!-- ICT Title -->
                <div class="col-lg-4 col-md-4 col-sm-4 col-12">
                    <h2 class="ICT-title">
                        <span
                            style="background:#000000;padding: 0px 5px;border-bottom: 1px solid #000000;color:#ffffff;">I</span>
                        <span
                            style="background:#1fb4da;padding: 0px 5px;border-bottom: 1px solid #1fb4da;color:#ffffff;">C</span>
                        <span
                            style="background:#1c63a9;padding: 0px 5px;border-bottom: 1px solid #1c63a9;color:#ffffff;">T</span>
                    </h2>
                    <h6 class="ICT-subtitle">Skill Development Programme</h6>
                </div>
                <!-- Spacer for layout -->
                <div class="col-lg-1 col-md-1 d-none d-md-block"></div>
                <!-- Inflibnet Logo -->
                <div class="col-lg-3 col-md-3 col-sm-4 col-12 inflibnet-logo text-end">
                    <img class="img-responsive" src="{{ asset('images/inflibnet.png') }}" alt="Inflibnet Logo">
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="#">HRD Training & Conference</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('participants.login') }}">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('participants.create_account') }}">Create Account</a>
                    </li>
                    <li class="nav-item">
                        <button class="btn btn-outline-light ms-3 toggle-theme">🌙 Dark Mode</button>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="content-wrapper">
        <div class="container mt-5 text-center">
            <h1 class="fw-bold">Welcome to HRD Training & Conference</h1>

        </div>
    </div>

    <!-- Fixed Footer -->
    <footer>
        <p class="mb-0">© {{ date('Y') }} INFLIBNET Centre, Gandhinagar. All rights reserved.</p>
    </footer>

    <!-- Load Bootstrap JS -->
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>

    <!-- Theme Toggle Script -->
    <script>
        document.querySelector('.toggle-theme').addEventListener('click', function() {
            var htmlElement = document.documentElement;
            var currentTheme = htmlElement.getAttribute("data-bs-theme");
            var newTheme = currentTheme === "light" ? "dark" : "light";
            htmlElement.setAttribute("data-bs-theme", newTheme);
            this.textContent = newTheme === "light" ? "🌙 Dark Mode" : "☀ Light Mode";
        });
    </script>
</body>

</html>
