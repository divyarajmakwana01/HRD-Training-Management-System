@extends('admin.layouts.admin_main')

@section('content')
    <div class="container">
        <h2><i class="bi bi-list-check"></i> Programmes Assigned to {{ $coordinator->name }}</h2>

        <!-- Flash Messages -->
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @elseif(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th><i class="bi bi-calendar-check"></i> Schedule</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($programmes as $programme)
                        <tr>
                            <td>{{ $programme->id }}</td>
                            <td>{{ $programme->name }}</td>
                            <td>
                                <i class="bi bi-calendar"></i>
                                {{ \Carbon\Carbon::parse($programme->startdate)->format('d M Y') }}
                                <i class="bi bi-clock"></i>
                                {{ \Carbon\Carbon::parse($programme->startTime)->format('h:i A') }}
                                <br>
                                <i class="bi bi-calendar"></i>
                                {{ \Carbon\Carbon::parse($programme->enddate)->format('d M Y') }}
                                <i class="bi bi-clock"></i>
                                {{ \Carbon\Carbon::parse($programme->endTime)->format('h:i A') }}
                            </td>
                            <td>
                                <!-- Remove Coordinator Button -->
                                <form
                                    action="{{ route('admin.programme_coordinators.destroy', [$programme->id, $coordinator->id]) }}"
                                    method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm"
                                        onclick="return confirm('Are you sure you want to remove this coordinator from the programme?')">
                                        <i class="bi bi-trash"></i> Remove
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">No programmes assigned to this coordinator.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Back to Coordinator List Button -->
        <div class="mt-3">
            <a href="{{ route('admin.coordinators.create') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i>
            </a>
        </div>
    </div>
@endsection
