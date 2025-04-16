@extends('admin.layouts.admin_main')

@section('title', 'Create Participant Account')

@section('content')

    <div class="container-fluid">
        <!-- Add New Button -->
        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addParticipantModal">
            Add New
        </button>
        <h2 class="mb-4">Participants List</h2>

        <form method="GET" action="{{ route('admin.create_participant') }}" class="mb-4">
            <div class="row align-items-end">
                <div class="col-md-4">
                    <label for="programme" class="form-label fw-bold">Select Programme</label>
                    <select name="programme" id="programme" class="form-select" required>
                        <option value="">-- Select Programme --</option>
                        @foreach ($programmes as $programme)
                            <option value="{{ $programme->id }}"
                                {{ isset($selectedProgramme) && $selectedProgramme == $programme->id ? 'selected' : '' }}>
                                {{ $programme->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary">Show Participants</button>
                </div>
            </div>
        </form>

        @if (isset($selectedProgramme) && $selectedProgramme && $programmeDetails)
            <div class="mt-4">
                <h5>Programme Details</h5>
                <ul class="list-group">
                    <li class="list-group-item"><strong>Programme Name:</strong> {{ $programmeDetails->name }}</li>
                    <li class="list-group-item"><strong>Brochure Link:</strong> <a
                            href="{{ $programmeDetails->brochure_link }}" target="_blank">Download Brochure</a></li>
                    <li class="list-group-item"><strong>Start Date:</strong>
                        {{ \Carbon\Carbon::parse($programmeDetails->startdate)->format('d M Y') }}</li>
                    <li class="list-group-item"><strong>End Date:</strong>
                        {{ \Carbon\Carbon::parse($programmeDetails->enddate)->format('d M Y') }}</li>
                    <li class="list-group-item"><strong>Start Time:</strong>
                        {{ \Carbon\Carbon::parse($programmeDetails->startTime)->format('h:i A') }}</li>
                    <li class="list-group-item"><strong>End Time:</strong>
                        {{ \Carbon\Carbon::parse($programmeDetails->endTime)->format('h:i A') }}</li>
                </ul>
            </div>
        @endif
        @if (isset($selectedProgramme) && $selectedProgramme)
            <form action="{{ route('admin.export.participants') }}" method="POST" class="mt-3">
                @csrf
                <input type="hidden" name="programme_id" id="programme_id" value="{{ $selectedProgramme }}">
                <button type="submit" class="btn btn-success">Export</button>
            </form>
            <br>
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover" style="font-size: 0.7rem;">
                    <thead class="table-light">
                        <tr>

                            <th>Name</th>
                            <th>Email</th>
                            <th>Mobile</th>
                            <th>Designation</th>
                            <th>Institute</th>
                            <th>City</th>
                            <th>State</th>
                            <th>Amount</th>
                            <th>Payment Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($participants as $participant)
                            <tr>

                                <td>{{ $participant->prefix }} {{ $participant->fname }} {{ $participant->lname }}</td>
                                <td>{{ $participant->email }}</td>
                                <td>{{ $participant->mobile }}</td>
                                <td>{{ $participant->designation }}</td>
                                <td>{{ $participant->institute_name }}</td>
                                <td>{{ $participant->city }}</td>
                                <td>
                                    {{ optional(collect($states)->firstWhere('state_code', $participant->state))->state_name ?? 'Unknown' }}
                                </td>

                                <td>{{ $participant->pamt ?? 'N/A' }}</td>
                                <td>
                                    @php
                                        $paymentStatus = ['Unverified', 'verified', 'Hold', 'Rejected'];
                                    @endphp
                                    {{ $paymentStatus[$participant->payment_verification] ?? 'Unknown' }}
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal"
                                        data-bs-target="#editParticipantModal{{ $participant->participant_id }}"
                                        data-programme-id="{{ $participant->programme_id }}">
                                        <i class="bi bi-pencil-fill"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                        data-bs-target="#detailsModal{{ $participant->participant_id }}"
                                        data-programme-id="{{ $participant->programme_id }}">
                                        <i class="bi bi-eye-fill"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-success" data-bs-toggle="modal"
                                        data-bs-target="#verifyModal{{ $participant->participant_id }}"
                                        data-programme-id="{{ $participant->programme_id }}">
                                        <i class="bi bi-check-circle"></i>
                                    </button>

                                </td>
                            </tr>

                            <!-- Edit Participant Modal -->
                            <div class="modal fade" id="editParticipantModal{{ $participant->participant_id }}"
                                tabindex="-1" aria-labelledby="editParticipantModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-xl">
                                    <div class="modal-content">
                                        <div class="modal-header bg-primary text-white">
                                            <h5 class="modal-title" id="editParticipantModalLabel">Edit Participant</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <form
                                            action="{{ route('admin.create_participant.update', $participant->participant_id) }}"
                                            class="row g-3" method="POST">
                                            <div class="modal-body">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="programme_id"
                                                    value="{{ $participant->programme_id }}">

                                                <h5 class="mt-2 mb-3 fw-bold text-primary">Personal Information</h5>
                                                <hr class="mb-3">
                                                <div class="row">
                                                    <input type="hidden" name="participant_id"
                                                        value="{{ $participant->participant_id }}">
                                                    <div class="col-md-2">
                                                        <label for="editPrefix" class="form-label fw-bold">Prefix</label>
                                                        <select class="form-select" id="editPrefix" name="prefix" required>
                                                            <option disabled value="">Choose...</option>
                                                            @foreach (['Mr.', 'Mrs.', 'Miss.', 'Dr.', 'Prof.'] as $prefix)
                                                                <option value="{{ $prefix }}"
                                                                    {{ ($participant->prefix ?? '') == $prefix ? 'selected' : '' }}>
                                                                    {{ $prefix }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label for="editFname" class="form-label fw-bold">First
                                                            Name</label>
                                                        <input type="text" class="form-control" id="editFname"
                                                            name="fname" value="{{ $participant->fname ?? '' }}"
                                                            required>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label for="editLname" class="form-label fw-bold">Last
                                                            Name</label>
                                                        <input type="text" class="form-control" id="editLname"
                                                            name="lname" value="{{ $participant->lname ?? '' }}"
                                                            required>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label for="editGender" class="form-label fw-bold">Gender</label>
                                                        <select class="form-select" id="editGender" name="gender"
                                                            required>
                                                            <option disabled value="">Choose...</option>
                                                            @foreach (['Male', 'Female'] as $gender)
                                                                <option value="{{ $gender }}"
                                                                    {{ ($participant->gender ?? '') == $gender ? 'selected' : '' }}>
                                                                    {{ $gender }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label for="editMobile" class="form-label fw-bold">Mobile</label>
                                                        <input type="text" class="form-control" id="editMobile"
                                                            name="mobile" value="{{ $participant->mobile ?? '' }}"
                                                            required>
                                                    </div>


                                                    <div class="col-md-3">
                                                        <label for="editCategory"
                                                            class="form-label fw-bold">Category</label>
                                                        <select name="category" id="editCategory" class="form-control">
                                                            <option value="SC"
                                                                {{ $participant->category == 'SC' ? 'selected' : '' }}>SC
                                                            </option>
                                                            <option value="ST"
                                                                {{ $participant->category == 'ST' ? 'selected' : '' }}>ST
                                                            </option>
                                                            <option value="OBC"
                                                                {{ $participant->category == 'OBC' ? 'selected' : '' }}>OBC
                                                            </option>
                                                            <option value="General"
                                                                {{ $participant->category == 'General' ? 'selected' : '' }}>
                                                                General</option>
                                                        </select>
                                                    </div>


                                                    <div class="col-md-3">
                                                        <label for="editEmail" class="form-label fw-bold">Email</label>
                                                        <input type="email" name="email"
                                                            value="{{ $participant->email }}" class="form-control"
                                                            id="editEmail" readonly>
                                                    </div>
                                                </div>

                                                <h5 class="mt-3 mb-3 fw-bold text-primary">Professional Details</h5>
                                                <hr class="mb-3">
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <label for="editDesignation"
                                                            class="form-label fw-bold">Designation</label>
                                                        <input type="text" class="form-control" id="editDesignation"
                                                            name="designation"
                                                            value="{{ $participant->designation ?? '' }}">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label for="editInstituteName"
                                                            class="form-label fw-bold">Institute Name</label>
                                                        <input type="text" class="form-control" id="editInstituteName"
                                                            name="institute_name"
                                                            value="{{ $participant->institute_name ?? '' }}">
                                                    </div>

                                                </div>

                                                <h5 class="mt-3 mb-3 fw-bold text-primary">Address and Location</h5>
                                                <hr class="mb-3">
                                                <div class="row">

                                                    <div class="col-md-3">
                                                        <label for="editCity" class="form-label fw-bold">City</label>
                                                        <input type="text" class="form-control" id="editCity"
                                                            name="city" value="{{ $participant->city ?? '' }}"
                                                            required>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label for="state" class="form-label fw-bold">State</label>
                                                        <select class="form-control" id="state" name="state"
                                                            required>
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
                                                        <label for="district" class="form-label fw-bold">District</label>
                                                        <select class="form-control" id="district" name="district"
                                                            required>
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
                                                        <label for="editCountry"
                                                            class="form-label fw-bold">Country</label>
                                                        <select class="form-select" id="editCountry" name="country">
                                                            <option value="">Select Country</option>
                                                            @foreach (['USA', 'Canada', 'India'] as $country)
                                                                <option value="{{ $country }}"
                                                                    {{ ($participant->country ?? '') == $country ? 'selected' : '' }}>
                                                                    {{ $country }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="row mt-3">


                                                    <div class="col-md-3">
                                                        <label for="editpincode"
                                                            class="form-label fw-bold">Pincode</label>
                                                        <input type="number" class="form-control" id="editpincode"
                                                            name="pincode" value="{{ $participant->pincode ?? '' }}">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="editAddress"
                                                            class="form-label fw-bold">Address</label>
                                                        <textarea class="form-control" id="editAddress" name="address" rows="2">{{ $participant->address ?? '' }}</textarea>
                                                    </div>

                                                </div>

                                                <h5 class="mt-3 mb-3 fw-bold text-primary">Social Links & Bio</h5>
                                                <hr class="mb-3">
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <label for="editFacebook"
                                                            class="form-label fw-bold">Facebook</label>
                                                        <input type="url" class="form-control" id="editFacebook"
                                                            name="facebook" value="{{ $participant->facebook ?? '' }}">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label for="editLinkedIn"
                                                            class="form-label fw-bold">LinkedIn</label>
                                                        <input type="url" class="form-control" id="editLinkedIn"
                                                            name="linkedin" value="{{ $participant->linkedin ?? '' }}">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label for="editTwitter"
                                                            class="form-label fw-bold">Twitter</label>
                                                        <input type="url" class="form-control" id="editTwitter"
                                                            name="twitter" value="{{ $participant->twitter ?? '' }}">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label for="editOrcid" class="form-label fw-bold">ORCid</label>
                                                        <input type="url" class="form-control" id="editOrcid"
                                                            name="orcid" value="{{ $participant->orcid ?? '' }}">
                                                    </div>
                                                </div>

                                                <div class="row mt-3">

                                                    <div class="col-md-6">
                                                        <label for="editBiography"
                                                            class="form-label fw-bold">Biography</label>
                                                        <textarea class="form-control" id="editBiography" name="biography" rows="2">{{ $participant->biography ?? '' }}</textarea>
                                                    </div>
                                                </div>

                                                <h5 class="mt-3 mb-3 fw-bold text-primary">Registration Details</h5>
                                                <hr class="mb-3">


                                                <div class="row mt-3">
                                                    <div class="col-md-3">
                                                        <label for="editPaymentMode" class="form-label fw-bold">Payment
                                                            Mode</label>
                                                        <select name="payment" class="form-control" id="editPaymentMode">
                                                            <option value="0"
                                                                {{ $participant->payment == 0 ? 'selected' : '' }}>Demand
                                                                Draft
                                                            </option>
                                                            <option value="1"
                                                                {{ $participant->payment == 1 ? 'selected' : '' }}>
                                                                NEFT/RTGS
                                                            </option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label for="editPno" class="form-label fw-bold">Transaction
                                                            id</label>
                                                        <input type="text" name="pno"
                                                            value="{{ $participant->pno }}" id="editPno"
                                                            class="form-control">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label for="editPbank" class="form-label fw-bold">Bank And
                                                            Branch</label>
                                                        <input type="text" name="pbank"
                                                            value="{{ $participant->pbank }}" id="editPbank"
                                                            class="form-control">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label for="editpamt" class="form-label fw-bold">Amount</label>
                                                        <input type="text" name="pamt"
                                                            value="{{ $participant->pamt }}" id="editbank"
                                                            class="form-control">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label for="editPdate" class="form-label fw-bold">Payment
                                                            Date</label>
                                                        <input type="date" name="pdate"
                                                            value="{{ $participant->pdate }}" id="editPdate"
                                                            class="form-control">
                                                    </div>
                                                </div>

                                                <h5 class="mt-3 mb-3 fw-bold text-primary">Admin Payment Remarks</h5>
                                                <hr class="mb-3">
                                                <div class="row">
                                                    <div class="row mt-3">
                                                        <div class="col-md-3">
                                                            <label for="editPaymentVerification"
                                                                class="form-label fw-bold">Payment Status</label>
                                                            <select name="payment_verification" class="form-control"
                                                                id="editPaymentVerification">
                                                                <option value="0"
                                                                    {{ $participant->payment_verification == 0 ? 'selected' : '' }}>
                                                                    Unverified
                                                                </option>
                                                                <option value="1"
                                                                    {{ $participant->payment_verification == 1 ? 'selected' : '' }}>
                                                                    Verified
                                                                </option>
                                                                <option value="2"
                                                                    {{ $participant->payment_verification == 2 ? 'selected' : '' }}>
                                                                    Hold
                                                                </option>
                                                                <option value="3"
                                                                    {{ $participant->payment_verification == 3 ? 'selected' : '' }}>
                                                                    Rejected
                                                                </option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label for="editPaymentRemarks"
                                                                class="form-label fw-bold">Payment Remarks</label>
                                                            <textarea class="form-control" id="editPaymentRemarks" name="payment_remarks" rows="2">{{ $participant->payment_remarks }}</textarea>
                                                        </div>
                                                    </div>
                                                </div>

                                                <h5 class="mt-3 mb-3 fw-bold text-primary">Travel Details</h5>
                                                <hr class="mb-3">
                                                <div class="row">
                                                    <div class="row mt-3">
                                                        <div class="col-md-4">
                                                            <label for="editArrivalDate"
                                                                class="form-label fw-bold">Arrival Date</label>
                                                            <input type="datetime-local" name="adate"
                                                                value="{{ $participant->adate }}" id="editArrivalDate"
                                                                class="form-control">
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label for="editReturnDate" class="form-label fw-bold">Return
                                                                Date</label>
                                                            <input type="datetime-local" name="rdate"
                                                                value="{{ $participant->rdate }}" id="editReturnDate"
                                                                class="form-control">
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label for="mode" class="form-label">Mode of
                                                                Travel:</label>
                                                            <select name="mode" class="form-select" required>
                                                                <option value="">Select Travel Mode</option>
                                                                <option value="Air"
                                                                    {{ $participant->mode == 'Air' ? 'selected' : '' }}>Air
                                                                </option>
                                                                <option value="Train"
                                                                    {{ $participant->mode == 'Train' ? 'selected' : '' }}>
                                                                    Train
                                                                </option>
                                                                <option value="Bus"
                                                                    {{ $participant->mode == 'Bus' ? 'selected' : '' }}>Bus
                                                                </option>
                                                                <option value="Car"
                                                                    {{ $participant->mode == 'Car' ? 'selected' : '' }}>Car
                                                                </option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- <h5 class="mt-3 mb-3 fw-bold text-primary">Passport Details</h5>
                                                <hr class="mb-3">
                                                <div class="form-group mb-2">
                                                    <label>
                                                        <input type="checkbox" id="foreign_delegate_edit"
                                                            onchange="togglePassport('edit_passport_details', 'foreign_delegate_edit')">
                                                        For
                                                        Foreign Delegates Only
                                                    </label>
                                                </div>
                                                <div id="edit_passport_details">
                                                    <div class="row mb-2">
                                                        <div class="col-md-6">
                                                            <label for="passno_edit" class="form-label">Passport
                                                                No:</label>
                                                            <input type="text" class="form-control" name="passno_edit"
                                                                disabled>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label for="editNationality"
                                                                class="form-label">Nationality:</label>
                                                            <select name="nation_edit" class="form-select"
                                                                id="editNationality" disabled>
                                                                <option value="">Select Country</option>
                                                                <option value="USA">USA</option>
                                                                <option value="Canada">Canada</option>
                                                                <option value="UK">UK</option>
                                                                <option value="Australia">Australia</option>
                                                                <option value="Germany">Germany</option>
                                                                <option value="France">France</option>
                                                                <option value="Japan">Japan</option>
                                                                <option value="China">China</option>
                                                                <option value="India">India</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label for="editpassv" class="form-label">Valid Upto:</label>
                                                            <input type="date" class="form-control" name="passv_edit"
                                                                disabled>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label for="place_of_birth_edit" class="form-label">Place of
                                                                Birth:</label>
                                                            <input type="text" class="form-control" name="pob_edit"
                                                                disabled>
                                                        </div>
                                                    </div>
                                                </div> --}}
                                            </div>

                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-primary">Save Changes</button>
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Close</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Details Modal -->
                            <div class="modal fade" id="detailsModal{{ $participant->participant_id }}" tabindex="-1"
                                aria-labelledby="detailsModalLabel{{ $participant->participant_id }}" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header bg-primary text-white">
                                            <h5 class="modal-title"
                                                id="detailsModalLabel{{ $participant->participant_id }}">
                                                <i class="bi bi-person-circle"></i> Participant Details
                                            </h5>
                                            <button type="button" class="btn-close btn-close-white"
                                                data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <!-- Left Column -->
                                                <div class="col-md-6">
                                                    <div class="mb-2"><i class="bi bi-person"></i>
                                                        <strong>Name:</strong> {{ $participant->prefix }}
                                                        {{ $participant->fname }} {{ $participant->lname }}
                                                    </div>
                                                    <div class="mb-2"><i class="bi bi-envelope"></i>
                                                        <strong>Email:</strong> {{ $participant->email ?? 'N/A' }}
                                                    </div>
                                                    <div class="mb-2"><i class="bi bi-telephone"></i>
                                                        <strong>Mobile:</strong> {{ $participant->mobile ?? 'N/A' }}
                                                    </div>
                                                    <div class="mb-2"><i class="bi bi-gender-ambiguous"></i>
                                                        <strong>Gender:</strong> {{ $participant->gender ?? 'N/A' }}
                                                    </div>
                                                    <div class="mb-2"><i class="bi bi-briefcase"></i>
                                                        <strong>Designation:</strong>
                                                        {{ $participant->designation ?? 'N/A' }}
                                                    </div>
                                                    <div class="mb-2"><i class="bi bi-building"></i> <strong>Institute
                                                            Name:</strong> {{ $participant->institute_name ?? 'N/A' }}
                                                    </div>
                                                    <div class="mb-2"><i class="bi bi-globe"></i>
                                                        <strong>Country:</strong> {{ $participant->country ?? 'N/A' }}
                                                    </div>
                                                    <div class="mb-2"><i class="bi bi-info-circle"></i>
                                                        <strong>Biography:</strong> {{ $participant->biography ?? 'N/A' }}
                                                    </div>
                                                </div>

                                                <!-- Right Column -->
                                                <div class="col-md-6">
                                                    <div class="mb-2"><i class="bi bi-house"></i>
                                                        <strong>Accommodation:</strong>
                                                        {{ $participant->accommodation ? 'Yes' : 'No' }}
                                                    </div>
                                                    <div class="mb-2"><i class="bi bi-house-check"></i>
                                                        <strong>Accommodation Category:</strong>
                                                        {{ $participant->acc_cat ?? 'N/A' }}
                                                    </div>
                                                    <div class="mb-2"><i class="bi bi-wallet2"></i> <strong>Payment
                                                            Status:</strong> {{ $participant->payment ? 'Yes' : 'No' }}
                                                    </div>
                                                    <div class="mb-2"><i class="bi bi-credit-card"></i> <strong>Payment
                                                            Mode:</strong>
                                                        @if ($participant->payment == 0)
                                                            Demand Draft
                                                        @elseif($participant->payment == 1)
                                                            NEFT/RTGS
                                                        @else
                                                            N/A
                                                        @endif
                                                    </div>
                                                    <div class="mb-2"><i class="bi bi-cash"></i> <strong>Payment
                                                            Amount:</strong> {{ $participant->pamt ?? 'N/A' }}</div>
                                                    <div class="mb-2"><i class="bi bi-calendar"></i> <strong>Payment
                                                            Date:</strong> {{ $participant->pdate ?? 'N/A' }}</div>
                                                    <div class="mb-2"><i class="bi bi-calendar-event"></i>
                                                        <strong>Arrival Date:</strong> {{ $participant->adate ?? 'N/A' }}
                                                    </div>
                                                    <div class="mb-2"><i class="bi bi-calendar-check"></i>
                                                        <strong>Return Date:</strong> {{ $participant->rdate ?? 'N/A' }}
                                                    </div>
                                                    <div class="mb-2"><i class="bi bi-airplane"></i> <strong>Mode of
                                                            Travel:</strong> {{ $participant->mode ?? 'N/A' }}</div>
                                                </div>
                                            </div>

                                            <!-- Social Media Links -->
                                            <div class="mt-3">
                                                <h5 class="text-primary"><i class="bi bi-share"></i> Social Media</h5>
                                                <hr>
                                                <div class="mb-2"><i class="bi bi-facebook"></i>
                                                    <strong>Facebook:</strong> <a href="{{ $participant->facebook }}"
                                                        target="_blank">{{ $participant->facebook ?? 'N/A' }}</a>
                                                </div>
                                                <div class="mb-2"><i class="bi bi-linkedin"></i>
                                                    <strong>LinkedIn:</strong> <a href="{{ $participant->linkedin }}"
                                                        target="_blank">{{ $participant->linkedin ?? 'N/A' }}</a>
                                                </div>
                                                <div class="mb-2"><i class="bi bi-twitter"></i>
                                                    <strong>Twitter:</strong> <a href="{{ $participant->twitter }}"
                                                        target="_blank">{{ $participant->twitter ?? 'N/A' }}</a>
                                                </div>
                                                <div class="mb-2"><i class="bi bi-person-badge"></i>
                                                    <strong>ORCid:</strong> <a href="{{ $participant->orcid }}"
                                                        target="_blank">{{ $participant->orcid ?? 'N/A' }}</a>
                                                </div>
                                            </div>

                                            <!-- Address Section -->
                                            <div class="mt-3">
                                                <h5 class="text-primary"><i class="bi bi-geo-alt"></i> Address</h5>
                                                <hr>
                                                <div class="mb-2"><i class="bi bi-house-door"></i>
                                                    <strong>Address:</strong> {{ $participant->address ?? 'N/A' }},
                                                    {{ $participant->city ?? 'N/A' }}
                                                </div>
                                                @php
                                                    $stateName = 'N/A';
                                                    $districtName = 'N/A';

                                                    if (!empty($participant->state)) {
                                                        $state = collect($states)
                                                            ->where('state_code', $participant->state)
                                                            ->first();
                                                        $stateName = $state->state_name ?? 'N/A';
                                                    }

                                                    if (!empty($participant->district)) {
                                                        $district = collect($districts)
                                                            ->where('district_code', $participant->district)
                                                            ->first();
                                                        $districtName = $district->district_name ?? 'N/A';
                                                    }
                                                @endphp
                                                <div class="mb-2"><i class="bi bi-map"></i> <strong>District:</strong>
                                                    {{ $districtName }}</div>
                                                <div class="mb-2"><i class="bi bi-geo"></i> <strong>State:</strong>
                                                    {{ $stateName }}</div>
                                                <div class="mb-2"><i class="bi bi-mailbox"></i>
                                                    <strong>Pincode:</strong> {{ $participant->pincode ?? 'N/A' }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Verification Modal -->
                            <div class="modal fade" id="verifyModal{{ $participant->participant_id }}" tabindex="-1"
                                aria-labelledby="verifyModalLabel{{ $participant->participant_id }}" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header bg-warning text-white">
                                            <h5 class="modal-title"
                                                id="verifyModalLabel{{ $participant->participant_id }}">
                                                <i class="bi bi-check-circle"></i> Verify Participant Responses
                                            </h5>
                                            <button type="button" class="btn-close btn-close-white"
                                                data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <h5 class="text-primary"><i class="bi bi-list-check"></i> Responses</h5>
                                            <ul class="list-group">
                                                @foreach ($participant->responses as $response)
                                                    <li class="list-group-item">
                                                        <strong>Q:</strong> {{ $response->question_text }}<br>
                                                        <strong>Answer:</strong> {{ $response->response }} <br>
                                                        <strong>Status:</strong>
                                                        @if ($response->active == 1)
                                                            <span class="badge bg-warning">Pending</span>
                                                        @elseif($response->active == 2)
                                                            <span class="badge bg-success">Verified</span>
                                                        @endif
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>

                                        <!-- Verify Button (Only One for All Questions) -->
                                        <div class="modal-footer">
                                            @if (collect($participant->responses)->contains('active', 1))
                                                <form
                                                    action="{{ route('admin.verifyResponse', $participant->participant_id) }}"
                                                    method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success">
                                                        <i class="bi bi-check-circle"></i> Verify All Responses
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-muted">Please select a programme to view the participant list.</p>
        @endif
    </div>
    </div>

    <!-- Add New Participant Modal -->
    <div class="modal fade" id="addParticipantModal" tabindex="-1" aria-labelledby="addParticipantModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="addParticipantModalLabel">Add New Participant</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.create_participant.store') }}" class="row g-3" method="POST">
                    <div class="modal-body">
                        @csrf
                        <div class="row">

                            <!-- Programme Details Section -->
                            <h5 class="mt-3 mb-3 fw-bold text-primary">Programme Details</h5>
                            <hr class="mb-3">
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="programme_id" class="form-label">Select Programme</label>
                                    <select name="programme_id" id="programme_id" class="form-select" required>
                                        <option value="" disabled selected>Select Programme</option>
                                        @foreach ($programmes as $programme)
                                            <option value="{{ $programme->id }}">{{ $programme->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4" id="groupNameField" style="display: none;">
                                <label for="regGroupName" class="form-label">Group Name</label>
                                <input type="text" name="reg_group_name" id="regGroupName" class="form-control"
                                    placeholder="Enter group name">
                            </div>
                        </div>

                        <h5 class="mt-2 mb-3 fw-bold text-primary">Personal Information</h5>
                        <hr class="mb-3">
                        <div class="row">
                            <div class="col-md-2">
                                <label for="addPrefix" class="form-label fw-bold">Prefix</label>
                                <select class="form-select" id="addPrefix" name="prefix" required>
                                    <option disabled selected value="">Choose...</option>
                                    @foreach (['Mr.', 'Mrs.', 'Miss.', 'Dr.', 'Prof.'] as $prefix)
                                        <option value="{{ $prefix }}">{{ $prefix }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="addFname" class="form-label fw-bold">First Name</label>
                                <input type="text" class="form-control" id="addFname" name="fname" required>
                            </div>
                            <div class="col-md-3">
                                <label for="addLname" class="form-label fw-bold">Last Name</label>
                                <input type="text" class="form-control" id="addLname" name="lname" required>
                            </div>
                            <div class="col-md-3">
                                <label for="addGender" class="form-label fw-bold">Gender</label>
                                <select class="form-select" id="addGender" name="gender" required>
                                    <option disabled selected value="">Choose...</option>
                                    @foreach (['Male', 'Female'] as $gender)
                                        <option value="{{ $gender }}">{{ $gender }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="addMobile" class="form-label fw-bold">Mobile</label>
                                <input type="text" class="form-control" id="addMobile" name="mobile" required>
                            </div>
                            <div class="col-md-3">
                                <label for="addEmail" class="form-label fw-bold">Email</label>
                                <input type="email" name="email" class="form-control" id="addEmail" required>
                            </div>
                        </div>

                        <h5 class="mt-3 mb-3 fw-bold text-primary">Professional Details</h5>
                        <hr class="mb-3">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="addDesignation" class="form-label fw-bold">Designation</label>
                                <input type="text" class="form-control" id="addDesignation" name="designation">
                            </div>
                            <div class="col-md-3">
                                <label for="addInstituteName" class="form-label fw-bold">Institute Name</label>
                                <input type="text" class="form-control" id="addInstituteName" name="institute_name">
                            </div>

                        </div>

                        <h5 class="mt-3 mb-3 fw-bold text-primary">Address and Location</h5>
                        <hr class="mb-3">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="addAddress" class="form-label fw-bold">Address</label>
                                <textarea class="form-control" id="addAddress" name="address" rows="2"></textarea>
                            </div>
                            <div class="col-md-3">
                                <label for="addCity" class="form-label fw-bold">City</label>
                                <input type="text" class="form-control" id="addCity" name="city" required>
                            </div>
                            <div class="col-md-3">
                                <label for="addState" class="form-label">State</label>
                                <select class="form-control" id="addState" name="state_code" required>
                                    <option value="">Select State</option>
                                    @foreach ($states as $state)
                                        <option value="{{ $state->state_code }}">{{ $state->state_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-3">
                                <label for="addDistrict" class="form-label">District</label>
                                <select class="form-control" id="addDistrict" name="district" required>
                                    <option value="">Select District</option>
                                    @foreach ($districts as $district)
                                        <option value="{{ $district->district_code }}"
                                            data-state="{{ $district->state_code }}">
                                            {{ $district->district_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="addCountry" class="form-label fw-bold">Country</label>
                                <select class="form-select" id="addCountry" name="country">
                                    <option value="">Select Country</option>
                                    @foreach (['USA', 'Canada', 'India'] as $country)
                                        <option value="{{ $country }}">{{ $country }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="addPincode" class="form-label fw-bold">Pincode</label>
                                <input type="number" class="form-control" id="addPincode" name="pincode">
                            </div>

                        </div>

                        <h5 class="mt-3 mb-3 fw-bold text-primary">Social Links & Bio</h5>
                        <hr class="mb-3">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="addFacebook" class="form-label fw-bold">Facebook</label>
                                <input type="url" class="form-control" id="addFacebook" name="facebook">
                            </div>
                            <div class="col-md-4">
                                <label for="addLinkedIn" class="form-label fw-bold">LinkedIn</label>
                                <input type="url" class="form-control" id="addLinkedIn" name="linkedin">
                            </div>
                            <div class="col-md-4">
                                <label for="addTwitter" class="form-label fw-bold">Twitter</label>
                                <input type="url" class="form-control" id="addTwitter" name="twitter">
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-4">
                                <label for="addOrcid" class="form-label fw-bold">ORCid</label>
                                <input type="url" class="form-control" id="addOrcid" name="orcid">
                            </div>
                            <div class="col-md-12">
                                <label for="addBiography" class="form-label fw-bold">Biography</label>
                                <textarea class="form-control" id="addBiography" name="biography" rows="2"></textarea>
                            </div>
                        </div>

                        <h5 class="mt-3 mb-3 fw-bold text-primary">Registration Details</h5>
                        <hr class="mb-3">
                        {{-- <div class="row">
                            <div class="col-md-6">
                                <label for="addAccommodation" class="form-label fw-bold">Accommodation</label>
                                <input type="text" name="accommodation" id="addAccommodation" class="form-control">
                            </div>

                            <div class="col-md-6">
                                <label for="addAcc_cat" class="form-label fw-bold">acc_cat</label>
                                <input type="text" name="acc_cat" id="addAcc_cat" class="form-control">
                            </div>
                        </div> --}}
                        <div class="col-md-6">
                            <label for="addCategory" class="form-label fw-bold">Category</label>
                            <input type="text" name="category" id="addcategory" class="form-control">
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <label for="addPaymentMode" class="form-label fw-bold">Payment Mode</label>
                                <select name="payment" class="form-control" id="addPaymentMode">
                                    <option value="0">Demand Draft</option>
                                    <option value="1">NEFT/RTGS</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="addPno" class="form-label fw-bold">Transaction id</label>
                                <input type="text" name="pno" id="addPno" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label for="addPbank" class="form-label fw-bold">Bank And Branch</label>
                                <input type="text" name="pbank" id="addPbank" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label for="addPamt" class="form-label fw-bold">Amount</label>
                                <input type="text" name="pamt" id="addPamt" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label for="pdate" class="form-label fw-bold">Payment Date</label>
                                <input type="date" name="pdate" id="pdate" class="form-control">
                            </div>
                        </div>

                        <h5 class="mt-3 mb-3 fw-bold text-primary">Admin Payment Remarks</h5>
                        <hr class="mb-3">
                        <div class="row">
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <label for="addPaymentVerification" class="form-label fw-bold">Payment Status</label>
                                    <select name="payment_verification" class="form-control" id="addPaymentVerification">
                                        <option value="0">Unverified</option>
                                        <option value="1">Verified</option>
                                        <option value="2">Hold</option>
                                        <option value="3">Rejected</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="addPaymentRemarks" class="form-label fw-bold">Payment Remarks</label>
                                    <textarea class="form-control" id="addPaymentRemarks" name="payment_remarks" rows="2"></textarea>
                                </div>
                            </div>
                        </div>

                        <h5 class="mt-3 mb-3 fw-bold text-primary">Travel Details</h5>
                        <hr class="mb-3">
                        <div class="row">
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <label for="addArrivalDate" class="form-label fw-bold">Arrival Date</label>
                                    <input type="datetime-local" name="adate" id="addArrivalDate"
                                        class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label for="addReturnDate" class="form-label fw-bold">Return Date</label>
                                    <input type="datetime-local" name="rdate" id="addReturnDate" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label for="mode" class="form-label">Mode of Travel:</label>
                                    <select name="mode" class="form-select" required>
                                        <option value="">Select Travel Mode</option>
                                        <option value="Air">Air</option>
                                        <option value="Train">Train</option>
                                        <option value="Bus">Bus</option>
                                        <option value="Car">Car</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <h5 class="mt-3 mb-3 fw-bold text-primary">Passport Details</h5>
                        <hr class="mb-3">
                        <div class="form-group mb-2">
                            <label>
                                <input type="checkbox" id="foreign_delegate_add"
                                    onchange="togglePassport('add_passport_details', 'foreign_delegate_add')"> For Foreign
                                Delegates Only
                            </label>
                        </div>
                        <div id="add_passport_details">
                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <label for="passno" class="form-label">Passport No:</label>
                                    <input type="text" class="form-control" name="passno" disabled>
                                </div>
                                <div class="col-md-6">
                                    <label for="addNationality" class="form-label">Nationality:</label>
                                    <select name="nation" class="form-select" id="addNationality" disabled>
                                        <option value="">Select Country</option>
                                        <option value="USA">USA</option>
                                        <option value="Canada">Canada</option>
                                        <option value="UK">UK</option>
                                        <option value="Australia">Australia</option>
                                        <option value="Germany">Germany</option>
                                        <option value="France">France</option>
                                        <option value="Japan">Japan</option>
                                        <option value="China">China</option>
                                        <option value="India">India</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="addpassv" class="form-label">Valid Upto:</label>
                                    <input type="date" class="form-control" name="passv" disabled>
                                </div>
                                <div class="col-md-6">
                                    <label for="place_of_birth" class="form-label">Place of Birth:</label>
                                    <input type="text" class="form-control" name="pob" disabled>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Save</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function toggleGroupNameField() {
            let regTypeSelect = document.getElementById('regType');
            let groupNameField = document.getElementById('groupNameField');
            let regGroupNameInput = document.getElementById('regGroupName');

            if (regTypeSelect.value === '2') {
                groupNameField.style.display = 'block';
                regGroupNameInput.setAttribute('required', 'true');
            } else {
                groupNameField.style.display = 'none';
                regGroupNameInput.removeAttribute('required');
            }
        }

        document.addEventListener("DOMContentLoaded", function() {
            const stateSelect = document.getElementById("addState");
            const districtSelect = document.getElementById("addDistrict");

            function filterDistricts() {
                const selectedState = stateSelect.value;

                // Get all district options
                const districtOptions = districtSelect.querySelectorAll("option");

                // Show only districts that match the selected state
                districtOptions.forEach(option => {
                    if (option.value === "") {
                        option.hidden = false; // Keep the default "Select District" option visible
                    } else {
                        option.hidden = option.getAttribute("data-state") !== selectedState;
                    }
                });

                // Reset district selection if the selected district does not match the new state
                if (!districtSelect.querySelector("option:not([hidden])")?.selected) {
                    districtSelect.value = "";
                }
            }

            // Run filter on state change
            stateSelect.addEventListener("change", filterDistricts);

            // Run once on page load to set correct districts
            filterDistricts();
        });

        function togglePassport(passportDetailsId, checkboxId) {
            const isChecked = document.getElementById(checkboxId).checked;
            const passportDetails = document.getElementById(passportDetailsId);

            if (passportDetails) {
                passportDetails.querySelectorAll('input, select').forEach(el => {
                    el.disabled = !isChecked;
                });
            }
        }

        // Ensure the function runs when the modals open (e.g., preselect values if needed)
        document.addEventListener("DOMContentLoaded", function() {
            togglePassport('add_passport_details', 'foreign_delegate_add');
        });
    </script>

@endsection
