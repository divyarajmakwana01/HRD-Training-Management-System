@extends('participants.layouts.participant_main')

@section('title', 'Dashboard')

@section('content')
    <div class="container mt-4">
        <div class="row">
            @php
                $data = [
                    [
                        'title' => 'Webinars',
                        'count' => $webinars ?? 0,
                        'icon' => 'bi-camera-reels-fill',
                        'bg' => 'primary',
                    ],
                    [
                        'title' => 'User Awareness',
                        'count' => $userAwareness ?? 0,
                        'icon' => 'bi-bell-fill',
                        'bg' => 'success',
                    ],
                    ['title' => 'Workshops', 'count' => $workshops ?? 0, 'icon' => 'bi-tools', 'bg' => 'warning'],
                    ['title' => 'Trainings', 'count' => $trainings ?? 0, 'icon' => 'bi-book-fill', 'bg' => 'danger'],
                    [
                        'title' => 'Collaborative',
                        'count' => $collaborative ?? 0,
                        'icon' => 'bi-people-fill',
                        'bg' => 'info',
                    ],
                    [
                        'title' => 'Participants',
                        'count' => $participants ?? 0,
                        'icon' => 'bi-person-fill',
                        'bg' => 'secondary',
                    ],
                ];
            @endphp

            @foreach ($data as $item)
                <div class="col-md-6 col-xl-4 mb-4">
                    <div class="card border-0 shadow">
                        <div class="card-body d-flex align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">{{ $item['title'] }}</h6>
                                <h3 class="mb-0 fw-bold">{{ $item['count'] }}</h3>
                            </div>
                            <i class="bi {{ $item['icon'] }} text-{{ $item['bg'] }} ms-auto display-6"></i>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- CHART SECTION -->
    <div class="container mt-4">
        <div class="row">
            <div class="col-lg-12">
                <div class="card shadow border-0">
                    <div class="card-body">
                        <h5 class="card-title">Programme Distribution</h5>
                        <div style="width: 100%; height: 400px;"> <!-- Increased height -->
                            <canvas id="programmeChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- CHART.JS -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var ctx = document.getElementById('programmeChart').getContext('2d');
            var programmeChart = new Chart(ctx, {
                type: 'pie', // Change from 'bar' to 'pie'
                data: {
                    labels: ['Webinars', 'User Awareness', 'Workshops', 'Trainings', 'Collaborative',
                        'Participants'
                    ],
                    datasets: [{
                        label: 'Programme Count',
                        data: [{{ $webinars }}, {{ $userAwareness }}, {{ $workshops }},
                            {{ $trainings }}, {{ $collaborative }}, {{ $participants }}
                        ],
                        backgroundColor: ['#007bff', '#28a745', '#ffc107', '#dc3545', '#6610f2',
                            '#17a2b8'
                        ],
                        hoverOffset: 10 // Adds spacing when hovered
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        }
                    }
                }
            });
        });
    </script>

@endsection
