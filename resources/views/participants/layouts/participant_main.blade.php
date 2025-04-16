<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HRD</title>
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/bootstrap-icons.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/bootstrap-icons.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/all.min.css') }}" rel="stylesheet" />


</head>

<body>
    @php
        $email = session('user_email');
        $user = DB::select('SELECT * FROM participants WHERE email = ?', [$email]);
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
                                <i class="bi bi-house-door"></i><span class="d-none d-sm-inline ms-2">Paticipants</span>
                            </a>
                        </div>
                    </nav>

                    <nav class="nav flex-column bg-dark p-3 vh-100">
                        <a class="nav-link text-white" href="{{ route('participants.dashboard') }}">
                            <i class="bi bi-speedometer2"></i> <span class="ms-2">Dashboard</span>
                        </a>

                        <a class="nav-link text-white" href="{{ route('participants.profile') }}">
                            <i class="bi bi-person"></i> <span class="ms-2">Profile</span>
                        </a>
                        <a class="nav-link text-white" href="{{ route('participants.programme') }}">
                            <i class="bi bi-calendar-event"></i> <span class="ms-2">Programme</span>
                        </a>

                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-10 col-sm-9 col-xl-10 p-0 m-0">
                <nav class="navbar navbar-expand-lg bg-light mb-3 shadow-sm">
                    <div class="container-fluid">
                        <ul class="navbar-nav ms-auto align-items-center">
                            @if($user)
                                    <li class="nav-item d-flex align-items-center">
                                        <img src="{{ asset('storage/' . $user->image) }}" alt="User Image"
                                            class="rounded-circle border" style="width: 40px; height: 40px;">
                                        <span class="ms-2 fw-bold">{{ $user->prefix }} {{ $user->fname }}
                                            {{ $user->lname }}</span>
                                    </li>
                            @endif
                            <li class="nav-item ms-3">
                                <form action="{{ route('participants.logout') }}" method="POST">
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
    <script src="{{ asset('js/captcha.js') }}"></script>
</body>

</html>
