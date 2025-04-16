@extends('coordinators.layouts.coordinator_main')

@section('content')
    <div class="container mt-4">
        <h1 class="mb-4">Programmes Assigned to You</h1>

        @if ($programmes->isEmpty())
            <div class="alert alert-warning">No programmes assigned.</div>
        @else
            <table class="table table-striped table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Programme</th>
                        <th>Brief</th>
                        <th>Schedule</th> <!-- Start Date & End Date in One Column -->
                    </tr>
                </thead>
                <tbody>
                    @foreach ($programmes as $programme)
                        @php
                            $startDate = \Carbon\Carbon::parse($programme->startdate);
                            $endDate = \Carbon\Carbon::parse($programme->enddate);
                        @endphp
                        <tr>
                            <td>{{ $programme->id }}</td>
                            <td>
                                <strong>{{ $programme->name }}</strong><br>
                                <small class="text-muted">
                                    <i class="bi bi-geo-alt-fill text-danger"></i>
                                    {{ $programme->programmeVenue }}
                                </small>
                            </td>
                            <td>{{ $programme->programmeBrief }}</td>
                            <td>
                                <i class="bi bi-calendar-check text-success"></i>
                                {{ $startDate->format('d M Y') }}
                                <i class="bi bi-arrow-right"></i>
                                <i class="bi bi-calendar-check text-danger"></i>
                                {{ $endDate->format('d M Y') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection
