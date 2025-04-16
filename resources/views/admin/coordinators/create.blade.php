@extends('admin.layouts.admin_main')

@section('content')
    <div class="container">
        <h2><i class="bi bi-person-plus"></i> Register Coordinator</h2>

        <!-- Flash Messages -->
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @elseif(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <!-- Coordinator Registration Form -->
        <div class="card shadow-sm p-3 mb-4">
            <form action="{{ route('admin.coordinators.register') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label"><i class="bi bi-envelope"></i> Email</label>
                    <input type="email" name="email" class="form-control" required
                        placeholder="Enter Coordinator's Email">
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-person-check"></i> Register
                </button>
            </form>
        </div>
    </div>

    <!-- Coordinator List Table -->
    <div class="container mt-4">
        <h2><i class="bi bi-people"></i> Coordinator List</h2>

        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Designation</th>
                        <th>Mobile</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($coordinators as $coordinator)
                        <tr>
                            <td>{{ $coordinator->id }}</td>
                            <td>{{ $coordinator->name ?? 'N/A' }}</td>
                            <td>{{ $coordinator->email }}</td>
                            <td>{{ $coordinator->designation ?? 'N/A' }}</td>
                            <td>{{ $coordinator->mobile ?? 'N/A' }}</td>
                            <td>
                                <!-- View Button -->
                                <a href="{{ route('admin.coordinators.show', $coordinator->id) }}"
                                    class="btn btn-info btn-sm">
                                    <i class="bi bi-eye"></i>
                                </a>

                                <!-- Edit Button -->
                                <a href="{{ route('admin.coordinators.edit', $coordinator->id) }}"
                                    class="btn btn-warning btn-sm">
                                    <i class="bi bi-pencil-square"></i>
                                </a>

                                <!-- Total Programme Assign List Button -->
                                <!-- Update the Programmes Button in the Coordinator List Table -->
                                <a href="{{ route('admin.coordinators.programmes', $coordinator->id) }}"
                                    class="btn btn-success btn-sm">
                                    <i class="bi bi-list-check"></i>
                                </a>
                                <!-- Delete Button -->
                                <form action="{{ route('admin.coordinators.destroy', $coordinator->id) }}" method="POST"
                                    style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm"
                                        onclick="return confirm('Are you sure you want to delete this coordinator?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">No coordinators found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
