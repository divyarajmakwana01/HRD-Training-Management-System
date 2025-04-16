<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HRD Admin Panel</title>
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/bootstrap-icons.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/bootstrap-icons.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/all.min.css') }}" rel="stylesheet" />


</head>

<body>
    @php
        $email = session('admin_username'); // Fetch email from session
        $user = DB::select('SELECT * FROM login WHERE email = ?', [$email]);
        $user = $user ? $user[0] : null;
    @endphp
    <nav class="navbar navbar-expand-lg navbar-light"
        style="background: linear-gradient(to right, #f8f9fa, #495057); padding: 1rem;">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <img src="{{ asset('images/HRDlogo.png') }}" alt="HRD Logo" class="img-fluid" style="max-height: 50px;">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <h2 class="text-light mb-0 me-3">HRD 2025</h2>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container-fluid">
        <div class="row" style="height: 100vh">
            <div class="col-2 col-sm-3 col-xl-2 bg-dark">
                <div class="sticky-top">
                    <nav class="navbar bg-dark border-bottom border-body mb-3" data-bs-theme="dark">
                        <div class="container-fluid">
                            <a class="navbar-brand" href="#">
                                <i class="bi bi-house-door"></i><span class="d-none d-sm-inline ms-2">Admin</span>
                            </a>
                        </div>
                    </nav>

                    <nav class="nav flex-column bg-dark p-1 vh-100">
                        <a class="nav-link text-white" href="{{ route('admin.dashboard') }}">
                            <i class="bi bi-speedometer"></i> <span class="ms-2">Dashboard</span>
                        </a>

                        <a class="nav-link text-white" href="{{ route('admin.programme') }}">
                            <i class="bi bi-calendar-check"></i> <span class="ms-2">Programme</span>
                        </a>

                        <a class="nav-link text-white" href="{{ route('admin.coordinators.create') }}">
                            <i class="bi bi-person-badge"></i> <span class="ms-2">Coordinator</span>
                        </a>

                        <a class="nav-link text-white" href="{{ route('admin.create_participant') }}">
                            <i class="bi bi-people"></i> <span class="ms-2">Participant</span>
                        </a>

                        <a class="nav-link text-white" href="{{ route('admin.programme_questions.index') }}">
                            <i class="bi bi-ui-checks"></i> <span class="ms-2">Questionnaire Survey</span>
                        </a>

                        <a class="nav-link text-white" href="{{ route('admin.questionnaire_programme.index') }}">
                            <i class="bi bi-card-checklist"></i> <span class="ms-2">Questionnaire </span>
                        </a>
                    </nav>

                </div>
            </div>

            <div class="col-10 col-sm-9 col-xl-10 p-0 m-0">
                <nav class="navbar navbar-expand-lg bg-body-tertiary mb-3">
                    <div class="container-fluid">
                        <ul class="navbar-nav ms-auto align-items-center">
                            @if ($user)
                                <li class="nav-item d-flex align-items-center">
                                    <i class="bi bi-person-circle me-2"></i> <!-- Profile Icon -->
                                    <span class="fw-bold">{{ $user->email }}</span>
                                </li>
                            @endif

                            <li class="nav-item ms-3">
                                <form action="{{ route('admin.logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        <i class="bi bi-box-arrow-right me-2"></i> Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </nav>

                <main>
                    @yield('content')
                </main>
            </div>
        </div>
    </div>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="../js/captcha.js"></script>


</body>

</html>
