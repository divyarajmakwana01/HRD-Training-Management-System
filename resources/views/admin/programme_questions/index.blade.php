@extends('admin.layouts.admin_main')

@section('content')
    <div class="container mt-4">
        <h2>Programme Questions</h2>

        <!-- Add Question Button (Opens Modal) -->
        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addQuestionModal">
            Add Question
        </button>

        <!-- Success Message -->
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <!-- Questions List Table -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Question</th>
                    <th>Answer Type</th>
                    <th>Answer Option</th>
                    <th>Answer Validation</th>
                    <th>Max Response</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($questions as $q)
                    <tr>
                        <td>{{ $q->id }}</td>
                        <td>{{ $q->questions }}</td>
                        <td>
                            @switch($q->answerType)
                                @case(1)
                                    Checkbox
                                @break

                                @case(2)
                                    Radio Button
                                @break

                                @case(3)
                                    Text
                                @break

                                @case(4)
                                    Likert Scale
                                @break
                            @endswitch
                        </td>
                        <td>{{ $q->answerOption }}</td>
                        <td>{{ $q->answerValidation }}</td>
                        <td>{{ $q->maxResponse }}</td>
                        <td>
                            <!-- Toggle Switch -->
                            <label class="switch">
                                <input type="checkbox" class="toggle-status" data-id="{{ $q->id }}"
                                    {{ $q->active ? 'checked' : '' }}>
                                <span class="slider"></span>
                            </label>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Add Question Modal -->
    <div class="modal fade" id="addQuestionModal" tabindex="-1" aria-labelledby="addQuestionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addQuestionModalLabel">Add Programme Question</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('admin.programme_questions.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Question</label>
                            <input type="text" name="questions" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Answer Type</label>
                            <select name="answerType" class="form-control" required>
                                <option value="1">Checkbox</option>
                                <option value="2">Radio Button</option>
                                <option value="3">Text</option>
                                <option value="4">Likert Scale</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Answer Option (Comma Separated)</label>
                            <input type="text" name="answerOption" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Answer Validation</label>
                            <input type="text" name="answerValidation" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Max Response</label>
                            <input type="text" name="maxResponse" class="form-control">
                        </div>




                        <button type="submit" class="btn btn-success">Save Question</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap & jQuery -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- SweetAlert2 CSS & JS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <!-- AJAX Script for Toggle -->
    <script>
        $(document).ready(function() {
            $('.toggle-status').change(function() {
                var questionId = $(this).data('id');
                var newStatus = $(this).is(':checked') ? 1 : 0;

                $.ajax({
                    url: "{{ route('admin.programme_questions.toggle_status') }}",
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        id: questionId,
                        active: newStatus
                    },
                    success: function(response) {
                        // Show SweetAlert2 popup
                        Swal.fire({
                            icon: 'success',
                            title: 'Updated',
                            text: 'Participant status has been updated successfully!',
                            confirmButtonColor: '#00bcd4'
                        });
                    },
                    error: function() {
                        // Show error alert if something goes wrong
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Something went wrong! Please try again.',
                            confirmButtonColor: '#d33'
                        });
                    }
                });
            });
        });
    </script>


    <!-- Custom CSS for Toggle Switch -->
    <style>
        .switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 25px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 34px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 4px;
            bottom: 3px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked+.slider {
            background-color: #00bcd4;
        }

        input:checked+.slider:before {
            transform: translateX(24px);
        }
    </style>
@endsection
