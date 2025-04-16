@extends('admin.layouts.admin_main')

@section('content')
    <!-- Include Bootstrap Icons -->

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-lg">
                    <div class="card-header bg-primary text-white text-center">
                        <h4 class="mb-0"><i class="bi bi-person-badge"></i> Coordinator Details</h4>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6"><i class="bi bi-hash"></i> <strong>ID:</strong> {{ $coordinator->id }}
                            </div>
                            <div class="col-md-6"><i class="bi bi-person"></i> <strong>Name:</strong>
                                {{ $coordinator->name }}</div>
                            <div class="col-md-6"><i class="bi bi-envelope"></i> <strong>Email:</strong>
                                {{ $coordinator->email }}</div>
                            <div class="col-md-6"><i class="bi bi-telephone"></i> <strong>Mobile:</strong>
                                {{ $coordinator->mobile }}</div>
                            <div class="col-md-6"><i class="bi bi-briefcase"></i> <strong>Designation:</strong>
                                {{ $coordinator->designation }}</div>
                            <div class="col-md-6"><i class="bi bi-diagram-3"></i> <strong>Division:</strong>
                                {{ $coordinator->division }}</div>
                            <div class="col-md-6"><i class="bi bi-phone"></i> <strong>Contact No:</strong>
                                {{ $coordinator->contact_no }}</div>
                            <div class="col-md-6"><i class="bi bi-info-circle"></i> <strong>Biography:</strong>
                                {{ $coordinator->biography }}</div>
                            <div class="col-md-6"><i class="bi bi-person-badge"></i> <strong>ORCID:</strong>
                                {{ $coordinator->orcid ?? 'N/A' }}</div>
                            <div class="col-md-6">
                                <i class="bi bi-facebook"></i> <strong>Facebook:</strong>
                                @if ($coordinator->facebook)
                                    <a href="{{ $coordinator->facebook }}" target="_blank">Facebook</a>
                                @else
                                    N/A
                                @endif
                            </div>
                            <div class="col-md-6">
                                <i class="bi bi-linkedin"></i> <strong>LinkedIn:</strong>
                                @if ($coordinator->linkedin)
                                    <a href="{{ $coordinator->linkedin }}" target="_blank">LinkedIn</a>
                                @else
                                    N/A
                                @endif
                            </div>
                            <div class="col-md-6">
                                <i class="bi bi-twitter"></i> <strong>Twitter:</strong>
                                @if ($coordinator->twitter)
                                    <a href="{{ $coordinator->twitter }}" target="_blank">Twitter</a>
                                @else
                                    N/A
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-center">
                        <a href="{{ route('admin.coordinators.create') }}" class="btn btn-secondary"><i
                                class="bi bi-arrow-left"></i> Back to List</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
