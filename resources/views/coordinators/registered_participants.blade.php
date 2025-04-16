@extends('coordinators.layouts.coordinator_main')

@section('content')
    <div class="container mt-4">
        <h2 class="mb-4">Registered Participants</h2>

        @if (count($participants) > 0)
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th class="text-center">SR No.</th>
                            <th>Participant Name</th>
                            <th>Email</th>
                            <th>Mobile</th>
                            <th>Programme</th>
                            <th class="text-center">Dates</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($participants as $index => $participant)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td><strong>{{ $participant->fname }} {{ $participant->lname }}</strong></td>
                                <td>{{ $participant->email }}</td>
                                <td><i class="bi bi-telephone text-success"></i> {{ $participant->mobile }}</td>
                                <td><strong>{{ $participant->programme_name }}</strong></td>
                                <td class="text-center">
                                    <i class="bi bi-calendar-event text-primary"></i>
                                    {{ \Carbon\Carbon::parse($participant->startdate)->format('d M Y') }}
                                    -
                                    {{ \Carbon\Carbon::parse($participant->enddate)->format('d M Y') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="alert alert-warning">No registered participants found.</div>
        @endif
    </div>
@endsection
