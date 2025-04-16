@extends('participants.layouts.participant_main')

@section('content')
    @php
        use Carbon\Carbon;

        // Programme Type Mapping
        $programmeTypes = ['Other', 'Webinar', 'User Awareness', 'Workshop', 'Training', 'Collaborative'];
    @endphp

    <div class="container">
        <h1>Available Programmes</h1>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Programme Name & Type</th> <!-- Merged Programme Type Here -->
                    <th>Programme Brief</th>
                    <th>Date & Time</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($programmes as $programme)
                    <tr>
                        <td>{{ $programme->id }}</td>
                        <td>
                            <strong>
                                {{ $programme->name }}
                                @if ($programme->brochure_link)
                                    <br>
                                    <a href="{{ $programme->brochure_link }}" target="_blank">
                                        <i class="bi bi-file-earmark-pdf-fill"></i> View Brochure
                                    </a>
                                @endif
                            </strong>
                            <br>
                            <span class="badge bg-info">
                                {{-- Ensure programmeType is a valid index in the array --}}
                                {{ isset($programmeTypes[$programme->programmeType]) ? $programmeTypes[$programme->programmeType] : 'Other' }}
                            </span>
                            <br>
                            <i class="bi bi-geo-alt"></i> {{ $programme->programmeVenue }}
                        </td>
                        <td>{{ $programme->programmeBrief }}</td>
                        <td>
                            <div>
                                <p><i class="bi bi-calendar"></i> <strong>Date:</strong></p>
                                <p>
                                    {{ Carbon::parse($programme->startdate)->format('F d, Y') }} -
                                    {{ Carbon::parse($programme->enddate)->format('F d, Y') }}
                                </p>
                                <p><i class="bi bi-clock"></i> <strong>Time:</strong></p>
                                <p>
                                    {{ Carbon::parse($programme->startTime)->format('h:i A') }} -
                                    {{ Carbon::parse($programme->endTime)->format('h:i A') }}
                                </p>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex justify-content-start align-items-center">

                                <!-- View Questions Button -->
                                <button class="btn btn-outline-dark btn-sm ms-2" data-bs-toggle="modal"
                                    data-bs-target="#questionModal{{ $programme->id }}">
                                    <i class="bi bi-question-circle-fill"></i>
                                </button>
                                <!-- Register Button -->
                                <button class="btn btn-outline-primary btn-sm ms-2" title="Register" data-bs-toggle="modal"
                                    data-bs-target="#registerModal{{ $programme->id }}">
                                    <i class="bi bi-person-plus-fill"></i>
                                </button>

                                <!-- Payment Button -->
                                <button class="btn btn-outline-success btn-sm ms-2" title="Payment" data-bs-toggle="modal"
                                    data-bs-target="#paymentModal{{ $programme->id }}">
                                    <i class="bi bi-credit-card-fill"></i>
                                </button>

                                <!-- Transport Button -->
                                <button class="btn btn-outline-info btn-sm ms-2" title="Transport" data-bs-toggle="modal"
                                    data-bs-target="#transportModal{{ $programme->id }}">
                                    <i class="bi bi-airplane-fill"></i>
                                </button>

                                <!-- PDF Button -->
                                <button class="btn btn-outline-danger btn-sm ms-2" title="Download PDF"
                                    data-bs-toggle="modal" data-bs-target="#pdfModal{{ $programme->id }}">
                                    <i class="bi bi-file-pdf-fill"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>


    <!-- Custom CSS -->
    <style>
        .custom-modal-size {
            max-width: 1000px;
            /* Set your preferred width */
            width: 100%;
            /* Responsive width */
        }
    </style>
    <!-- Modals for Registration -->
    @foreach ($programmes as $programme)
        <div class="modal fade" id="registerModal{{ $programme->id }}" tabindex="-1">
            <div class="modal-dialog custom-modal-size">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Register for {{ $programme->name }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('programme.register', $programme->id) }}" method="POST">
                            @csrf

                            <!-- Personal Information Card -->
                            <div class="card mb-3">
                                <div class="card-header bg-primary text-white">
                                    <h6>Personal Information</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-md-3">
                                            <label class="form-label">Prefix</label>
                                            <select class="form-select" name="prefix">
                                                <option value="Mr."
                                                    {{ optional($participant)->prefix == 'Mr.' ? 'selected' : '' }}>Mr.
                                                </option>
                                                <option value="Mrs."
                                                    {{ optional($participant)->prefix == 'Mrs.' ? 'selected' : '' }}>Mrs.
                                                </option>
                                                <option value="Ms."
                                                    {{ optional($participant)->prefix == 'Ms.' ? 'selected' : '' }}>Ms.
                                                </option>
                                                <option value="Dr."
                                                    {{ optional($participant)->prefix == 'Dr.' ? 'selected' : '' }}>Dr.
                                                </option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">First Name</label>
                                            <input type="text" class="form-control" name="first_name"
                                                value="{{ optional($participant)->fname ?? '' }}" required>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Last Name</label>
                                            <input type="text" class="form-control" name="last_name"
                                                value="{{ optional($participant)->lname ?? '' }}" required>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Designation</label>
                                            <input type="text" class="form-control" name="designation"
                                                value="{{ optional($participant)->designation ?? '' }}" required>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-3">
                                            <label class="form-label">Gender</label>
                                            <select class="form-select" name="gender">
                                                <option value="Male"
                                                    {{ optional($participant)->gender == 'Male' ? 'selected' : '' }}>Male
                                                </option>
                                                <option value="Female"
                                                    {{ optional($participant)->gender == 'Female' ? 'selected' : '' }}>
                                                    Female</option>
                                                <option value="Other"
                                                    {{ optional($participant)->gender == 'Other' ? 'selected' : '' }}>Other
                                                </option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Mobile</label>
                                            <input type="text" class="form-control" name="mobile"
                                                value="{{ optional($participant)->mobile ?? '' }}" required>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Category</label>
                                            <select class="form-control" name="category" required>
                                                <option value="">Select Category</option>
                                                <option value="SC"
                                                    {{ (optional($registration[$programme->id] ?? null)->category ?? '') == 'SC' ? 'selected' : '' }}>
                                                    SC</option>
                                                <option value="ST"
                                                    {{ (optional($registration[$programme->id] ?? null)->category ?? '') == 'ST' ? 'selected' : '' }}>
                                                    ST</option>
                                                <option value="OBC"
                                                    {{ (optional($registration[$programme->id] ?? null)->category ?? '') == 'OBC' ? 'selected' : '' }}>
                                                    OBC</option>
                                                <option value="General"
                                                    {{ (optional($registration[$programme->id] ?? null)->category ?? '') == 'General' ? 'selected' : '' }}>
                                                    General</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Email</label>
                                            <input type="email" class="form-control"
                                                value="{{ session('user_email') }}" disabled>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Address Information Card -->
                            <div class="card mb-3">
                                <div class="card-header bg-primary text-white">
                                    <h6>Address Information</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-md-3">
                                            <label class="form-label">Institute Name</label>
                                            <input type="text" class="form-control" name="institute_name"
                                                value="{{ optional($participant)->institute_name ?? '' }}" required>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">City</label>
                                            <input type="text" class="form-control" name="city"
                                                value="{{ optional($participant)->city ?? '' }}" required>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Pincode</label>
                                            <input type="text" class="form-control" name="pincode"
                                                value="{{ optional($participant)->pincode ?? '' }}" required>
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
                                            <label class="form-label">Country</label>
                                            <input type="text" class="form-control" name="country"
                                                value="{{ optional($participant)->country ?? '' }}" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Address</label>
                                            <input type="text" class="form-control" name="address"
                                                value="{{ optional($participant)->address ?? '' }}" required>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Social Media and Additional Information Card -->
                            <div class="card mb-3">
                                <div class="card-header bg-primary text-white">
                                    <h6>Social Media and Additional Information</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-md-3">
                                            <label class="form-label">Facebook</label>
                                            <input type="text" class="form-control" name="facebook"
                                                value="{{ optional($participant)->facebook ?? '' }}"
                                                placeholder="Facebook">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">LinkedIn</label>
                                            <input type="text" class="form-control" name="linkedin"
                                                value="{{ optional($participant)->linkedin ?? '' }}"
                                                placeholder="LinkedIn">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Twitter</label>
                                            <input type="text" class="form-control" name="twitter"
                                                value="{{ optional($participant)->twitter ?? '' }}"
                                                placeholder="Twitter">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">ORCID</label>
                                            <input type="text" class="form-control" name="orcid"
                                                value="{{ optional($participant)->orcid ?? '' }}">
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <label class="form-label">Biography</label>
                                            <textarea class="form-control" name="biography" rows="3">{{ optional($participant)->biography ?? '' }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Registration Details Card -->
                            {{-- <div class="card mb-3">
                                <div class="card-header bg-primary text-white">
                                    <h6>Registration Details</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-3">
                                        
                                        <div class="col-md-3">
                                            <label class="form-label">Accommodation</label>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="accommodation"
                                                    value="Yes"
                                                    {{ optional($registration[$programme->id] ?? null)->accommodation == 'Yes' ? 'checked' : '' }}>
                                                <label class="form-check-label">Yes</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="accommodation" value="No"
                                                    {{ optional($registration[$programme->id] ?? null)->accommodation == 'No' ? 'checked' : '' }}>
                                                <label class="form-check-label">No</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3" id="groupNameField" style="display: none;">
                                            <label class="form-label">Group Name</label>
                                            <input type="text" class="form-control" name="reg_group_name"
                                                value="{{ optional($registration[$programme->id] ?? null)->reg_group_name ?? '' }}">
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-3">
                                            <label class="form-label">Passport Number</label>
                                            <input type="text" class="form-control" name="passno"
                                                value="{{ optional($registration[$programme->id] ?? null)->passno ?? '' }}">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Passport Valid Until</label>
                                            <input type="date" class="form-control" name="passv"
                                                value="{{ optional($registration[$programme->id] ?? null)->passv ?? '' }}">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Place of Birth</label>
                                            <input type="text" class="form-control" name="pob"
                                                value="{{ optional($registration[$programme->id] ?? null)->pob ?? '' }}">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Nationality</label>
                                            <input type="text" class="form-control" name="nation"
                                                value="{{ optional($registration[$programme->id] ?? null)->nation ?? '' }}">
                                        </div>
                                    </div>
                                </div>
                            </div> --}}

                            {{-- <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    let regTypeSelect = document.querySelector("select[name='reg_type']");
                                    let groupNameField = document.getElementById("groupNameField");

                                    function toggleGroupField() {
                                        if (regTypeSelect.value == '2') {
                                            groupNameField.style.display = "block";
                                        } else {
                                            groupNameField.style.display = "none";
                                        }
                                    }

                                    regTypeSelect.addEventListener("change", toggleGroupField);
                                    toggleGroupField(); // Initial call to set correct visibility
                                });
                            </script> --}}

                            <button type="submit" class="btn btn-primary">
                                {{ isset($registration[$programme->id]) ? 'Update Registration' : 'Submit Registration' }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Modal -->
        @foreach ($programmes as $programme)
            <div class="modal fade" id="paymentModal{{ $programme->id }}" tabindex="-1"
                aria-labelledby="paymentModalLabel" aria-hidden="true">
                <div class="modal-dialog custom-modal-size">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Payment Details for {{ $programme->name }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form action="{{ route('participant.payment.store', $programme->id) }}" method="POST">
                                @csrf
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label for="pno" class="form-label">Payment No/Receipt</label>
                                        <input type="text" class="form-control" name="pno"
                                            value="{{ optional($registration[$programme->id] ?? null)->pno ?? '' }}"
                                            required>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="pbank" class="form-label">Bank Name</label>
                                        <input type="text" class="form-control" name="pbank"
                                            value="{{ optional($registration[$programme->id] ?? null)->pbank ?? '' }}"
                                            required>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="pdate" class="form-label">Payment Date</label>
                                        <input type="date" class="form-control" name="pdate"
                                            value="{{ optional($registration[$programme->id] ?? null)->pdate ?? '' }}"
                                            required>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="pamt" class="form-label">Amount Paid</label>
                                        <input type="text" class="form-control" name="pamt"
                                            value="{{ optional($registration[$programme->id] ?? null)->pamt ?? '' }}"
                                            required>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="payment" class="form-label fw-bold">Payment Mode</label>
                                        <select name="payment" class="form-control" id="payment">
                                            <option value="0"
                                                {{ isset($registration[$programme->id]) && $registration[$programme->id]->payment == '0' ? 'selected' : '' }}>
                                                Demand Draft
                                            </option>
                                            <option value="1"
                                                {{ isset($registration[$programme->id]) && $registration[$programme->id]->payment == '1' ? 'selected' : '' }}>
                                                NEFT/RTGS
                                            </option>
                                        </select>
                                    </div>

                                </div>

                                {{-- Check payment verification status --}}
                                @php
                                    $paymentStatus = isset($registration[$programme->id])
                                        ? $registration[$programme->id]->payment_verification
                                        : 0;
                                @endphp

                                @if ($paymentStatus == 1)
                                    <p class="text-success"><strong>Payment Verified ✅</strong></p>
                                @else
                                    @if ($paymentStatus == 2)
                                        <p class="text-warning"><strong>Payment On Hold ⏳</strong></p>
                                    @elseif ($paymentStatus == 3)
                                        <p class="text-danger"><strong>Payment Rejected ❌</strong></p>
                                    @else
                                        <p class="text-info"><strong>Payment Unverified ⚠️</strong></p>
                                    @endif
                                    {{-- Show button if payment is NOT verified --}}
                                    <button type="submit" class="btn btn-primary w-10">Save Payment Details</button>
                                @endif

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        <!-- Transport Modal -->
        @foreach ($programmes as $programme)
            <div class="modal fade" id="transportModal{{ $programme->id }}" tabindex="-1"
                aria-labelledby="transportModalLabel" aria-hidden="true">
                <div class="modal-dialog custom-modal-size">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Transport Details for {{ $programme->name }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form action="{{ route('participant.transport.store', $programme->id) }}" method="POST">
                                @csrf
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label for="mode" class="form-label">Mode of Transport</label>
                                        <select class="form-select" name="mode" required>
                                            <option value="Air"
                                                {{ isset($registration[$programme->id]) && $registration[$programme->id]->mode == 'Air' ? 'selected' : '' }}>
                                                Air</option>
                                            <option value="Train"
                                                {{ isset($registration[$programme->id]) && $registration[$programme->id]->mode == 'Train' ? 'selected' : '' }}>
                                                Train</option>
                                            <option value="Bus"
                                                {{ isset($registration[$programme->id]) && $registration[$programme->id]->mode == 'Bus' ? 'selected' : '' }}>
                                                Bus</option>
                                            <option value="Car"
                                                {{ isset($registration[$programme->id]) && $registration[$programme->id]->mode == 'Car' ? 'selected' : '' }}>
                                                Car</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="adate" class="form-label">Arrival Date</label>
                                        <input type="date" class="form-control" name="adate"
                                            value="{{ optional($registration[$programme->id] ?? null)->adate ?? '' }}"
                                            required>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="rdate" class="form-label">Return Date</label>
                                        <input type="date" class="form-control" name="rdate"
                                            value="{{ optional($registration[$programme->id] ?? null)->rdate ?? '' }}"
                                            required>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary w-10">Save Transport Details</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>



            <!-- Modal -->
            <!-- Modal -->
            <div class="modal fade" id="pdfModal{{ $programme->id }}" tabindex="-1"
                aria-labelledby="pdfModalLabel{{ $programme->id }}" aria-hidden="true">
                <div class="modal-dialog custom-modal-size">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="pdfModalLabel{{ $programme->id }}">Preview & Download PDF</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p>Do you want to preview or download the PDF for the <b>{{ $programme->name }}</b> programme?
                            </p>
                        </div>
                        <div class="modal-footer">
                            <!-- Preview Button -->
                            <a href="{{ route('generate.pdf', ['programme_id' => $programme->id, 'action' => 'preview']) }}"
                                target="_blank" class="btn btn-primary btn-sm">
                                <i class="bi bi-eye-fill"></i> Preview PDF
                            </a>
                            <!-- Download Button -->
                            <a href="{{ route('generate.pdf', ['programme_id' => $programme->id, 'action' => 'download']) }}"
                                class="btn btn-outline-danger btn-sm">
                                <i class="bi bi-file-pdf-fill"></i> Download PDF
                            </a>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        @foreach ($programmes as $programme)
            <!-- Question Modal for Programme -->
            <div class="modal fade" id="questionModal{{ $programme->id }}" tabindex="-1"
                aria-labelledby="questionModalLabel{{ $programme->id }}" aria-hidden="true">
                <div class="modal-dialog custom-modal-size">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="questionModalLabel{{ $programme->id }}">Programme Questions</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            @if (isset($programmeQuestions[$programme->id]) && count($programmeQuestions[$programme->id]) > 0)
                                <form action="{{ route('programme.submitResponse') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="programme_id" value="{{ $programme->id }}">
                                    <ul class="list-group">
                                        @foreach ($programmeQuestions[$programme->id] as $question)
                                            <li class="list-group-item">
                                                <strong>{{ $question->questions }}</strong>

                                                @php
                                                    $answerOptions = explode('|', $question->answerOption);
                                                    $userResponse =
                                                        $userResponses[$programme->id][$question->id] ?? null;
                                                    $isVerified =
                                                        isset(
                                                            $userResponses[$programme->id][$question->id . '_active'],
                                                        ) &&
                                                        $userResponses[$programme->id][$question->id . '_active'] == 2;
                                                @endphp

                                                <!-- Display Answer Type -->
                                                @if ($question->answerType == 1)
                                                    <!-- Checkbox -->
                                                    @foreach ($answerOptions as $option)
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox"
                                                                name="responses[{{ $question->id }}][]"
                                                                value="{{ trim($option) }}"
                                                                @if ($userResponse && in_array(trim($option), explode(', ', $userResponse))) checked @endif
                                                                @if ($isVerified) disabled @endif>
                                                            <label class="form-check-label">{{ trim($option) }}</label>
                                                        </div>
                                                    @endforeach
                                                @elseif($question->answerType == 2)
                                                    <!-- Radio Button -->
                                                    @foreach ($answerOptions as $option)
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="radio"
                                                                name="responses[{{ $question->id }}]"
                                                                value="{{ trim($option) }}"
                                                                @if ($userResponse == trim($option)) checked @endif
                                                                @if ($isVerified) disabled @endif>
                                                            <label class="form-check-label">{{ trim($option) }}</label>
                                                        </div>
                                                    @endforeach
                                                @elseif($question->answerType == 3)
                                                    <!-- Text Input -->
                                                    <input type="text" class="form-control"
                                                        name="responses[{{ $question->id }}]"
                                                        value="{{ $userResponse ?? '' }}" placeholder="Enter your answer"
                                                        @if ($isVerified) disabled @endif>
                                                @elseif($question->answerType == 4)
                                                    <!-- Likert Scale -->
                                                    <div class="d-flex justify-content-between">
                                                        @foreach (['1', '2', '3', '4', '5'] as $scale)
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="radio"
                                                                    name="responses[{{ $question->id }}]"
                                                                    value="{{ $scale }}"
                                                                    @if ($userResponse == $scale) checked @endif
                                                                    @if ($isVerified) disabled @endif>
                                                                <label
                                                                    class="form-check-label">{{ $scale }}</label>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <p class="text-muted">Invalid answer type.</p>
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>

                                    <!-- Show "Answer Verified" if responses are verified -->
                                    @if ($isVerified)
                                        <div class="alert alert-success mt-3">Answer Verified ✅</div>
                                    @else
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-primary">Submit</button>
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Close</button>
                                        </div>
                                    @endif
                                </form>
                            @else
                                <p class="text-muted">No questions available for this programme.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @endforeach
@endsection
