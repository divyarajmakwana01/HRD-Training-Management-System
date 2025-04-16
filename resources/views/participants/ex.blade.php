@extends('participants.layouts.participant_main')

@section('title', 'Profile Settings')

@section('content')
    <div class="container mt-4">
        <div class="px-3">
            <h1>Profile Settings</h1>

            <form method="GET" action="{{ route('participant.profile') }}">
                @csrf

                <!-- Input field for the participant ID -->
                <div class="col-md-4">
                    <label for="id" class="form-label">Participant ID</label>
                    <input type="number" class="form-control" id="id" name="id" required>
                    <div class="invalid-feedback">Please enter a valid ID.</div>
                </div>

                <!-- Button to submit the form and fetch data -->
                <button type="submit" class="btn btn-primary mt-2">Fetch Profile</button>

                <!-- Display fetched First Name and Last Name -->
                @if (isset($participant))
                    <div class="mt-3">
                        <div class="col-md-4">
                            <label for="fname" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="fname" name="fname"
                                value="{{ old('fname', $participant->fname) }}" required readonly>
                            <div class="invalid-feedback">Please enter a valid first name.</div>
                        </div>
                        <div class="col-md-4">
                            <label for="lname" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="lname" name="lname"
                                value="{{ old('lname', $participant->lname) }}" required readonly>
                            <div class="invalid-feedback">Please enter a valid last name.</div>
                        </div>
                    </div>
                @endif
            </form>
        </div>
    </div>
@endsection
