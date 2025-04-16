@extends('admin.layouts.admin_main')

@section('content')
    @php
        use Carbon\Carbon;

        // Programme Type Mapping
        $programmeTypes = ['Other', 'Webinar', 'User Awareness', 'Workshop', 'Training', 'Collaborative'];
    @endphp

    <div class="container">
        <h1>Programmes</h1>
        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addProgrammeModal">Add New</button>
        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

        <!-- Select2 CSS -->
        <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />

        <!-- jQuery (Required for Select2) -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

        <!-- Select2 JS -->
        <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>

        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

        <style>
            /* Custom styling for better Select2 display */
            .select2-container .select2-selection--multiple {
                min-height: 38px;
                border: 1px solid #ced4da;
                padding: 5px;
            }
        </style>


        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Programme Name & Type</th>
                    <th>Programme Brief</th>
                    <th>Date & Time</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($programmes as $programme)
                    <tr>
                        <td>{{ $programme->id }}</td>
                        <td>
                            <strong>{{ $programme->name }}</strong>
                            @if ($programme->brochure_link)
                                <br>
                                <a href="{{ $programme->brochure_link }}" target="_blank">
                                    <i class="bi bi-file-earmark-pdf-fill"></i> View Brochure
                                </a>
                            @endif
                            <br>
                            <span class="badge bg-info">
                                {{ isset($programmeTypes[$programme->programmeType]) ? $programmeTypes[$programme->programmeType] : 'Other' }}
                            </span>
                            <br>
                            <i class="bi bi-geo-alt"></i> {{ $programme->programmeVenue }}
                        </td>
                        <td>{{ $programme->programmeBrief }}</td>
                        <td>
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
                        </td>
                        <td>
                            <div class="d-flex justify-content-start align-items-center">
                                <form action="{{ route('programme.toggleStatus', $programme->id) }}" method="POST"
                                    style="display:inline;">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                        class="btn {{ $programme->active ? 'btn-success' : 'btn-danger' }} btn-sm">
                                        <i class="bi {{ $programme->active ? 'bi-toggle-on' : 'bi-toggle-off' }}"></i>
                                    </button>
                                </form>

                                <!-- Assign Coordinators Button (Single Button) -->
                                <button class="btn btn-primary btn-sm ms-2" data-bs-toggle="modal"
                                    data-bs-target="#addCoordinatorModal{{ $programme->id }}">
                                    <i class="bi bi-person-plus-fill"></i>
                                </button>

                                <button class="btn btn-outline-warning btn-sm ms-2" data-bs-toggle="modal"
                                    data-bs-target="#editProgrammeModal{{ $programme->id }}">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <form action="{{ route('admin.programme.destroy', $programme->id) }}" method="POST"
                                    style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm ms-2"
                                        onclick="return confirm('Are you sure?')">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>


                    <!-- Edit Programme Modal -->
                    <div class="modal fade" id="editProgrammeModal{{ $programme->id }}" tabindex="-1"
                        aria-labelledby="editProgrammeModalLabel{{ $programme->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-xl">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editProgrammeModalLabel{{ $programme->id }}">Edit Programme
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form action="{{ route('admin.programme.update', $programme->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')

                                        <div class="row g-3">
                                            <div class="col-md-4">
                                                <label class="form-label">Year</label>
                                                <input type="number" class="form-control" name="year"
                                                    value="{{ old('year', $programme->year) }}" required>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Programme Name</label>
                                                <input type="text" class="form-control" name="name"
                                                    value="{{ old('name', $programme->name) }}" required>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Programme Type</label>
                                                <select class="form-control" name="programmeType">
                                                    <option value="1"
                                                        {{ old('programmeType', $programme->programmeType) == 1 ? 'selected' : '' }}>
                                                        Webinar</option>
                                                    <option value="2"
                                                        {{ old('programmeType', $programme->programmeType) == 2 ? 'selected' : '' }}>
                                                        User Awareness</option>
                                                    <option value="3"
                                                        {{ old('programmeType', $programme->programmeType) == 3 ? 'selected' : '' }}>
                                                        Workshop</option>
                                                    <option value="4"
                                                        {{ old('programmeType', $programme->programmeType) == 4 ? 'selected' : '' }}>
                                                        Training</option>
                                                    <option value="5"
                                                        {{ old('programmeType', $programme->programmeType) == 5 ? 'selected' : '' }}>
                                                        Collaborative</option>
                                                </select>
                                            </div>

                                            <div class="col-md-4">
                                                <label class="form-label">Programme Brief</label>
                                                <textarea class="form-control" name="programmeBrief">{{ old('programmeBrief', $programme->programmeBrief) }}</textarea>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Brochure Link</label>
                                                <input type="url" class="form-control" name="brochure_link"
                                                    value="{{ old('brochure_link', $programme->brochure_link) }}">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Programme Venue</label>
                                                <input type="text" class="form-control" name="programmeVenue"
                                                    value="{{ old('programmeVenue', $programme->programmeVenue) }}">
                                            </div>

                                            <div class="col-md-4">
                                                <label class="form-label">Questionnaire</label>
                                                <select class="form-control" name="questionnaire">
                                                    <option value="">-- Select --</option>
                                                    <option value="Y"
                                                        {{ old('questionnaire', $programme->questionnaire) == 'Y' ? 'selected' : '' }}>
                                                        Yes</option>
                                                    <option value="N"
                                                        {{ old('questionnaire', $programme->questionnaire) == 'N' ? 'selected' : '' }}>
                                                        No</option>
                                                </select>
                                            </div>

                                            <div class="col-md-4">
                                                <label class="form-label">Start Date</label>
                                                <input type="date" class="form-control" name="startdate"
                                                    value="{{ old('startdate', $programme->startdate) }}" required>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">End Date</label>
                                                <input type="date" class="form-control" name="enddate"
                                                    value="{{ old('enddate', $programme->enddate) }}" required>
                                            </div>

                                            <div class="col-md-4">
                                                <label class="form-label">Start Time</label>
                                                <input type="time" class="form-control" name="startTime"
                                                    value="{{ old('startTime', $programme->startTime) }}">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">End Time</label>
                                                <input type="time" class="form-control" name="endTime"
                                                    value="{{ old('endTime', $programme->endTime) }}">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Fees</label>
                                                <input type="number" class="form-control" name="fees" step="0.01"
                                                    value="{{ old('fees', $programme->fees) }}">
                                            </div>

                                            <div class="col-md-4">
                                                <label class="form-label">Fees with Accommodation</label>
                                                <input type="number" class="form-control" name="fees_with_acc"
                                                    step="0.01"
                                                    value="{{ old('fees_with_acc', $programme->fees_with_acc) }}">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Fee Exemption</label>
                                                <select class="form-control" name="fees_exemption">
                                                    <option value="1"
                                                        {{ old('fees_exemption', $programme->fees_exemption) == 1 ? 'selected' : '' }}>
                                                        Yes</option>
                                                    <option value="0"
                                                        {{ old('fees_exemption', $programme->fees_exemption) == 0 ? 'selected' : '' }}>
                                                        No</option>
                                                </select>
                                            </div>
                                        </div> <!-- End Row -->

                                        <div class="modal-footer mt-3">
                                            <button type="submit" class="btn btn-primary">Update Programme</button>
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Add Coordinator Modal -->
                    <div class="modal fade" id="addCoordinatorModal{{ $programme->id }}" tabindex="-1"
                        aria-labelledby="addCoordinatorModalLabel{{ $programme->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="addCoordinatorModalLabel{{ $programme->id }}">Assign
                                        Coordinator</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form action="{{ route('admin.programme.addCoordinator', $programme->id) }}"
                                        method="POST">
                                        @csrf
                                        <div class="mb-3">
                                            <label class="form-label">Select Coordinator</label>
                                            <select class="form-control select2" name="coordinator_id[]" multiple
                                                required>
                                                @foreach ($coordinators as $coordinator)
                                                    <option value="{{ $coordinator->id }}">{{ $coordinator->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-primary">Assign Coordinator</button>
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>


                    <script>
                        $(document).ready(function() {
                            $('.select2').select2({
                                placeholder: "Select Coordinators",
                                allowClear: true
                            });

                            // Ensure Select2 works inside a Bootstrap modal
                            $('.modal').on('shown.bs.modal', function() {
                                $(this).find('.select2').select2({
                                    dropdownParent: $(this),
                                    placeholder: "Select Coordinators",
                                    allowClear: true
                                });
                            });
                        });
                    </script>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Add Programme Modal -->
    <div class="modal fade" id="addProgrammeModal" tabindex="-1" aria-labelledby="addProgrammeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addProgrammeModalLabel">Create Programme</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('admin.programme.store') }}" method="POST">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="year" class="form-label">Year</label>
                                <input type="number" class="form-control" name="year" required>
                            </div>
                            <div class="col-md-4">
                                <label for="name" class="form-label">Programme Name</label>
                                <input type="text" class="form-control" name="name" required>
                            </div>
                            <div class="col-md-4">
                                <label for="programmeType" class="form-label">Programme Type</label>
                                <select class="form-control" name="programmeType">
                                    <option value="1">Webinar</option>
                                    <option value="2">User Awareness</option>
                                    <option value="3">Workshop</option>
                                    <option value="4">Training</option>
                                    <option value="5">Collaborative</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label for="programmeVenue" class="form-label">Programme Venue</label>
                                <input type="text" class="form-control" name="programmeVenue"
                                    placeholder="Enter venue">
                            </div>
                            <div class="col-md-4">
                                <label for="programmeBrief" class="form-label">Programme Brief</label>
                                <textarea class="form-control" name="programmeBrief"></textarea>
                            </div>
                            <div class="col-md-4">
                                <label for="brochureLink" class="form-label">Brochure Link</label>
                                <input type="url" class="form-control" name="brochureLink">
                            </div>

                            <div class="col-md-4">
                                <label for="questionnaire" class="form-label">Questionnaire</label>
                                <select class="form-control" name="questionnaire" id="questionnaire">
                                    <option value="">-- Select --</option>
                                    <option value="Y">Yes</option>
                                    <option value="N">No</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label for="startdate" class="form-label">Start Date</label>
                                <input type="date" class="form-control" name="startdate" required>
                            </div>
                            <div class="col-md-4">
                                <label for="enddate" class="form-label">End Date</label>
                                <input type="date" class="form-control" name="enddate" required>
                            </div>

                            <div class="col-md-4">
                                <label for="startTime" class="form-label">Start Time</label>
                                <input type="time" class="form-control" name="startTime" required>
                            </div>
                            <div class="col-md-4">
                                <label for="endTime" class="form-label">End Time</label>
                                <input type="time" class="form-control" name="endTime" required>
                            </div>
                            <div class="col-md-4">
                                <label for="fees" class="form-label">Fees</label>
                                <input type="text" class="form-control" name="fees">
                            </div>

                            <div class="col-md-4">
                                <label for="fees_with_acc" class="form-label">Fees with Accommodation</label>
                                <input type="text" class="form-control" name="fees_with_acc">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Fee Exemption</label>
                                <select class="form-control" name="fees_exemption">
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                        </div> <!-- End Row -->

                        <!-- Modal Footer for Buttons -->
                        <div class="modal-footer mt-3">
                            <button type="submit" class="btn btn-primary">Save Programme</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
