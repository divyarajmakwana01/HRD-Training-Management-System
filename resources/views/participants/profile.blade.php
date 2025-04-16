@extends('participants.layouts.participant_main')

@section('title', 'Profile Settings')

@section('content')
    <div class="container mt-4">
        <div class="px-3">
            <h1><i class="bi bi-person-circle"></i> Participant Profile</h1>

            <!-- Flash Messages -->
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @php
                $email = session('user_email');
                $participant = DB::table('participants as p')
                    ->leftJoin('state_district as s', 'p.state', '=', 's.state_code')
                    ->leftJoin('state_district as d', 'p.district', '=', 'd.district_code')
                    ->select('p.*', 's.state_name', 'd.district_name')
                    ->where('p.email', $email)
                    ->first();
            @endphp

            @php
                $participant = $participant ?? null;
            @endphp

            <div class="row">
                <!-- Profile Information -->
                <div class="col-md-6">
                    <div class="card shadow-sm">
                        <div class="card-body text-center">
                            <img src="{{ isset($participant) && $participant->image ? asset('storage/' . $participant->image) : asset('images/default-profile.png') }}"
                                onerror="this.onerror=null;this.src='{{ asset('images/blank.png') }}';" alt="Profile Image"
                                class="rounded-circle mb-3 border border-secondary"
                                style="width: 120px; height: 120px; object-fit: cover;">



                            <h5 class="fw-bold">
                                {{ ($participant->prefix ?? '') . ' ' . ($participant->fname ?? '') . ' ' . ($participant->lname ?? '') }}
                            </h5>
                            <p class="text-muted"><i class="bi bi-envelope"></i> {{ session('user_email') }}</p>

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
                                {{ $participant->designation ?? 'N/A' }}</p>
                            <p><strong><i class="bi bi-gender-ambiguous"></i> Gender:</strong>
                                {{ $participant->gender ?? 'N/A' }}</p>
                            <p><strong><i class="bi bi-building"></i> Institute Name:</strong>
                                {{ $participant->institute_name ?? 'N/A' }}</p>
                            <p><strong><i class="bi bi-geo-alt"></i> Address:</strong> {{ $participant->address ?? 'N/A' }}
                            </p>
                            <p><strong><i class="bi bi-geo"></i> City:</strong> {{ $participant->city ?? 'N/A' }}</p>
                            <p><strong><i class="bi bi-geo"></i> PinCode:</strong> {{ $participant->pincode ?? 'N/A' }}</p>
                            <p><strong><i class="bi bi-map"></i> District:</strong>
                                {{ $participant->district_name ?? 'N/A' }}
                            </p>
                            <p><strong><i class="bi bi-flag"></i> State:</strong> {{ $participant->state_name ?? 'N/A' }}
                            </p>
                            <p><strong><i class="bi bi-globe"></i> Country:</strong> {{ $participant->country ?? 'N/A' }}
                            </p>
                            <p><strong><i class="bi bi-phone"></i> Mobile:</strong> {{ $participant->mobile ?? 'N/A' }}</p>
                            <p><strong><i class="bi bi-book"></i> Biography:</strong>
                                {{ $participant->biography ?? 'N/A' }}</p>

                            <div class="mt-3">
                                <p><strong><i class="bi bi-facebook text-primary"></i> Facebook:</strong>
                                    <a href="{{ optional($participant)->facebook ?: '#' }}" target="_blank">
                                        {{ optional($participant)->facebook ?: 'N/A' }}
                                    </a>
                                </p>
                                <p><strong><i class="bi bi-linkedin text-primary"></i> LinkedIn:</strong>
                                    <a href="{{ optional($participant)->linkedin ?: '#' }}" target="_blank">
                                        {{ optional($participant)->linkedin ?: 'N/A' }}
                                    </a>
                                </p>
                                <p><strong><i class="bi bi-twitter-x text-dark"></i> Twitter:</strong>
                                    <a href="{{ optional($participant)->twitter ?: '#' }}" target="_blank">
                                        {{ optional($participant)->twitter ?: 'N/A' }}
                                    </a>
                                </p>
                                <p><strong><i class="bi bi-person-badge"></i> ORCID:</strong>
                                    <a href="{{ optional($participant)->orcid ?: '#' }}" target="_blank">
                                        {{ optional($participant)->orcid ?: 'N/A' }}
                                    </a>
                                </p>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>


    <!-- Modal for Editing Profile -->
    <div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
        <div class="modal-dialog" style="max-width: 1000px;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editProfileModalLabel">Edit Profile</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form class="row g-3 needs-validation" action="{{ route('participants.profile.store') }}"
                        method="POST" enctype="multipart/form-data" novalidate>
                        @csrf

                        <!-- Profile Image -->
                        <div class="col-md-3">
                            <label for="image" class="form-label">Profile Image</label>
                            <input type="file" class="form-control" id="image" name="image" accept="image/*">

                            @if (optional($participant)->image)
                                <img src="{{ Storage::exists('public/' . $participant->image) ? Storage::url('public/' . $participant->image) : asset('images/default-profile.png') }}"
                                    onerror="this.onerror=null;this.src='{{ asset('images/default-profile.png') }}';"
                                    alt="Profile Image" class="img-thumbnail mt-2"
                                    style="width: 100px; height: 100px; object-fit: cover;">

                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" name="remove_image" id="remove_image">
                                    <label class="form-check-label" for="remove_image">Remove Profile Image</label>
                                </div>
                            @endif
                        </div>

                        <!-- Prefix -->
                        <div class="col-md-3">
                            <label for="prefix" class="form-label">Prefix</label>
                            <select class="form-select" id="prefix" name="prefix" required>
                                <option selected disabled value="">Choose...</option>
                                @foreach (['Mr.', 'Mrs.', 'Miss.', 'Dr.', 'Prof.'] as $option)
                                    <option value="{{ $option }}"
                                        {{ old('prefix', optional($participant)->prefix) == $option ? 'selected' : '' }}>
                                        {{ $option }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Name -->
                        <div class="col-md-3">
                            <label for="fname" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="fname" name="fname"
                                value="{{ old('fname', optional($participant)->fname ?? '') }}" required>
                        </div>

                        <div class="col-md-3">
                            <label for="lname" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="lname" name="lname"
                                value="{{ old('lname', optional($participant)->lname ?? '') }}" required>
                        </div>

                        <!-- Designation -->
                        <div class="col-md-3">
                            <label for="designation" class="form-label">Designation</label>
                            <input type="text" class="form-control" id="designation" name="designation"
                                value="{{ old('designation', optional($participant)->designation ?? '') }}" required>
                        </div>

                        <!-- Gender -->
                        <div class="col-md-3">
                            <label for="gender" class="form-label">Gender</label>
                            <select class="form-select" id="gender" name="gender" required>
                                <option selected disabled value="">Choose...</option>
                                <option value="Male"
                                    {{ old('gender', optional($participant)->gender) == 'Male' ? 'selected' : '' }}>Male
                                </option>
                                <option value="Female"
                                    {{ old('gender', optional($participant)->gender) == 'Female' ? 'selected' : '' }}>
                                    Female</option>
                                <option value="Other"
                                    {{ old('gender', optional($participant)->gender) == 'Other' ? 'selected' : '' }}>Other
                                </option>
                            </select>
                        </div>

                        <!-- Institute Name -->
                        <div class="col-md-3">
                            <label for="institute_name" class="form-label">Institute Name</label>
                            <input type="text" class="form-control" id="institute_name" name="institute_name"
                                value="{{ old('institute_name', optional($participant)->institute_name ?? '') }}"
                                required>
                        </div>



                        <!-- Address -->
                        <div class="col-md-3">
                            <label for="address" class="form-label">Address</label>
                            <input type="text" class="form-control" id="address" name="address"
                                value="{{ old('address', optional($participant)->address ?? '') }}" required>
                        </div>
                        <div class="col-md-3">
                            <label for="city" class="form-label">City</label>
                            <input type="text" class="form-control" id="city" name="city"
                                value="{{ old('city', optional($participant)->city ?? '') }}" required>
                        </div>

                        <!-- Pincode -->
                        <div class="col-md-3">
                            <label for="pincode" class="form-label">Pincode</label>
                            <input type="text" class="form-control" id="pincode" name="pincode"
                                value="{{ old('pincode', optional($participant)->pincode ?? '') }}" required>
                        </div>

                        <div class="col-md-3">
                            <label for="state" class="form-label">State</label>
                            <select class="form-control" id="state" name="state_code" required>
                                <option value="">Select State</option>
                                @foreach ($states as $state)
                                    <option value="{{ $state->state_code }}"
                                        {{ ($participant->state ?? '') == $state->state_code ? 'selected' : '' }}>
                                        {{ $state->state_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>


                        <div class="col-md-3">
                            <label for="district" class="form-label">District</label>
                            <select class="form-control" id="district" name="district_code" required>
                                <option value="">Select District</option>
                                @foreach ($districts as $district)
                                    <option value="{{ $district->district_code }}"
                                        data-state="{{ $district->state_code }}"
                                        {{ ($participant->district ?? '') == $district->district_code ? 'selected' : '' }}>
                                        {{ $district->district_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>


                        <div class="col-md-3">
                            <label for="country" class="form-label">Country</label>
                            <input type="text" class="form-control" id="country" name="country"
                                value="{{ old('country', optional($participant)->country ?? '') }}">
                        </div>

                        <!-- Contact and Social Media -->
                        <div class="col-md-3">
                            <label for="mobile" class="form-label">Mobile</label>
                            <input type="text" class="form-control" id="mobile" name="mobile"
                                value="{{ old('mobile', optional($participant)->mobile ?? '') }}">
                        </div>


                        <div class="col-md-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email"
                                value="{{ session('user_email') }}">
                        </div>

                        <div class="col-md-3">
                            <label for="facebook" class="form-label">Facebook</label>
                            <input type="text" class="form-control" id="facebook" name="facebook"
                                value="{{ optional($participant)->facebook ?? '' }}">
                        </div>

                        <div class="col-md-3">
                            <label for="linkedin" class="form-label">LinkedIn</label>
                            <input type="text" class="form-control" id="linkedin" name="linkedin"
                                value="{{ optional($participant)->linkedin ?? '' }}">
                        </div>

                        <div class="col-md-3">
                            <label for="twitter" class="form-label">Twitter</label>
                            <input type="text" class="form-control" id="twitter" name="twitter"
                                value="{{ optional($participant)->twitter ?? '' }}">
                        </div>

                        <div class="col-md-3">
                            <label for="orcid" class="form-label">ORCID</label>
                            <input type="text" class="form-control" id="orcid" name="orcid"
                                value="{{ optional($participant)->orcid ?? '' }}">
                        </div>

                        <div class="col-md-12">
                            <label for="biography" class="form-label">Biography</label>
                            <textarea class="form-control" id="biography" name="biography" rows="3">{{ optional($participant)->biography ?? '' }}</textarea>
                        </div>

                        <!-- Submit Button -->
                        <div class="col-12 text-center">
                            <button class="btn btn-primary px-4" type="submit">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let stateDropdown = document.getElementById('state');
            let districtDropdown = document.getElementById('district');

            function filterDistricts() {
                let selectedState = stateDropdown.value;
                let options = districtDropdown.options;
                let currentDistrict = districtDropdown.value; // Save the current district selection

                // Loop through all district options and show/hide based on selected state
                for (let i = 0; i < options.length; i++) {
                    let option = options[i];
                    if (option.getAttribute('data-state') === selectedState || option.value === "") {
                        option.style.display = 'block';
                    } else {
                        option.style.display = 'none';
                    }
                }

                // Keep the previously selected district if it is still visible
                if (currentDistrict) {
                    // Check if the previously selected district is still visible after filtering
                    let currentDistrictOption = districtDropdown.querySelector(option[value =
                        "${currentDistrict}"]);
                    if (currentDistrictOption && currentDistrictOption.style.display !== 'none') {
                        districtDropdown.value = currentDistrict; // Restore the previous selection
                    } else {
                        districtDropdown.value = ""; // If the selected district is not visible, reset it
                    }
                } else {
                    districtDropdown.value = ""; // No district selected, reset it
                }
            }

            // Run on page load and when the state changes
            filterDistricts();
            stateDropdown.addEventListener('change', filterDistricts);
        });
    </script>
@endsection
