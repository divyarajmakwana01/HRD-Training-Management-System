@extends('coordinators.layouts.coordinator_main')

@section('content')
    <div class="container mt-4">
        <div class="px-3">
            <h1><i class="bi bi-person-circle"></i> Coordinator Profile</h1>

            <!-- Flash Messages -->
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @php
                $coordinator = $coordinator ?? null;
            @endphp

            <div class="row">
                <!-- Profile Details -->
                <div class="col-md-6">
                    <div class="card shadow-sm">
                        <div class="card-body text-center">
                            <img src="{{ asset('storage/' . ($coordinator->image ?? 'images/default-profile.png')) }}"
                                onerror="this.onerror=null;this.src='{{ asset('images/blank.png') }}';" alt="Profile Image"
                                class="rounded-circle mb-3 border border-secondary"
                                style="width: 120px; height: 120px; object-fit: cover;">

                            <h5 class="fw-bold">{{ $coordinator->name ?? 'N/A' }}</h5>
                            <p class="text-muted"><i class="bi bi-envelope"></i> {{ $coordinator->email ?? 'N/A' }}</p>

                            <button class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                                <i class="bi bi-pencil-square"></i> Edit Profile
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Additional Information -->
                <div class="col-md-6">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <p><strong><i class="bi bi-briefcase"></i> Designation:</strong>
                                {{ $coordinator->designation ?? 'N/A' }}</p>
                            <p><strong><i class="bi bi-phone"></i> Mobile:</strong> {{ $coordinator->mobile ?? 'N/A' }}</p>
                            <p><strong><i class="bi bi-geo"></i> Division:</strong> {{ $coordinator->division ?? 'N/A' }}
                            </p>
                            <p><strong><i class="bi bi-phone"></i> Contact No.:</strong>
                                {{ $coordinator->contact_no ?? 'N/A' }}</p>
                            <p><strong><i class="bi bi-book"></i> Biography:</strong>
                                {{ $coordinator->biography ?? 'N/A' }}</p>

                            <div class="mt-3">
                                <p><strong><i class="bi bi-facebook text-primary"></i> Facebook:</strong>
                                    <a href="{{ $coordinator->facebook ?: '#' }}" target="_blank">
                                        {{ $coordinator->facebook ?: 'N/A' }}
                                    </a>
                                </p>
                                <p><strong><i class="bi bi-linkedin text-primary"></i> LinkedIn:</strong>
                                    <a href="{{ $coordinator->linkedin ?: '#' }}" target="_blank">
                                        {{ $coordinator->linkedin ?: 'N/A' }}
                                    </a>
                                </p>
                                <p><strong><i class="bi bi-twitter-x text-dark"></i> Twitter:</strong>
                                    <a href="{{ $coordinator->twitter ?: '#' }}" target="_blank">
                                        {{ $coordinator->twitter ?: 'N/A' }}
                                    </a>
                                </p>
                                <p><strong><i class="bi bi-person-badge"></i> ORCID:</strong>
                                    <a href="{{ $coordinator->orcid ?: '#' }}" target="_blank">
                                        {{ $coordinator->orcid ?: 'N/A' }}
                                    </a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Edit Profile Modal -->
    <div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg"> <!-- Adjusted for better responsiveness -->
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editProfileModalLabel">Edit Profile</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('coordinator.update', $coordinator->id ?? 0) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Full Name</label>
                                <input type="text" name="name" class="form-control"
                                    value="{{ optional($coordinator)->name }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Designation</label>
                                <input type="text" name="designation" class="form-control"
                                    value="{{ optional($coordinator)->designation }}" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Mobile</label>
                                <input type="text" name="mobile" class="form-control"
                                    value="{{ optional($coordinator)->mobile }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Contact Number</label>
                                <input type="text" name="contact_no" class="form-control"
                                    value="{{ optional($coordinator)->contact_no }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Division</label>
                            <input type="text" name="division" class="form-control"
                                value="{{ optional($coordinator)->division }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Biography</label>
                            <textarea name="biography" class="form-control" rows="3">{{ optional($coordinator)->biography }}</textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Facebook</label>
                                <input type="url" name="facebook" class="form-control"
                                    value="{{ optional($coordinator)->facebook }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">LinkedIn</label>
                                <input type="url" name="linkedin" class="form-control"
                                    value="{{ optional($coordinator)->linkedin }}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Twitter</label>
                                <input type="url" name="twitter" class="form-control"
                                    value="{{ optional($coordinator)->twitter }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">ORCID</label>
                                <input type="text" name="orcid" class="form-control"
                                    value="{{ optional($coordinator)->orcid }}">
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
