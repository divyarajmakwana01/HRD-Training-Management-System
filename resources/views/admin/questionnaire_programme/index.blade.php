@extends('admin.layouts.admin_main')

@section('content')
    <div class="container mt-4">
        <h2>Questionnaire Programme</h2>

        <!-- Programme Dropdown -->
        <div class="mb-3">
            <label for="programmeSelect" class="form-label">Select Programme:</label>
            <select id="programmeSelect" class="form-control">
                <option value="">-- Select Programme --</option>
                @foreach ($programmes as $programme)
                    <option value="{{ $programme->id }}">{{ $programme->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Questions List -->
        <form id="questionnaireForm">
            @csrf
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Select</th>
                        <th>ID</th>
                        <th>Questions</th>
                        <th>Answer Type</th>
                        <th>Answer Option</th>
                        <th>Answer Validation</th>
                        <th>Max Response</th>
                        <th>Sequence</th>
                        <th>Mandatory</th>
                    </tr>
                </thead>
                <tbody id="questionList">
                    <!-- Questions will be loaded dynamically -->
                </tbody>
            </table>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>

    <!-- Custom CSS for Toggle Button -->
    <style>
        .switch {
            position: relative;
            display: inline-block;
            width: 40px;
            height: 20px;
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
            border-radius: 20px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 14px;
            width: 14px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked+.slider {
            background-color: #28a745;
        }

        input:checked+.slider:before {
            transform: translateX(20px);
        }
    </style>

    <!-- AJAX Script -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#programmeSelect').change(function() {
                let programme_id = $(this).val();
                $('#questionList').html(''); // Clear previous questions

                if (programme_id) {
                    $.ajax({
                        url: "{{ route('admin.questionnaire_programme.fetch') }}",
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            programme_id: programme_id
                        },
                        success: function(response) {
                            if (response.questions.length > 0) {
                                response.questions.forEach(question => {
                                    let checked = question.mandatory === 'Yes' ?
                                        'checked' : '';
                                    let selected = question.assigned ? 'checked' : '';

                                    // Prevent duplicate rows
                                    if ($(`#question-row-${question.id}`).length ===
                                        0) {
                                        $('#questionList').append(`
                                    <tr id="question-row-${question.id}">
                                        <td>
                                            <input type="checkbox" class="select-question" data-question-id="${question.id}" ${selected}>
                                        </td>
                                        <td>${question.id}</td>
                                        <td>${question.questions ?? 'N/A'}</td>
                                        <td>${question.answerTypeText ?? 'N/A'}</td>
                                        <td>${question.answerOption ?? 'N/A'}</td>
                                        <td>${question.answerValidation ?? 'N/A'}</td>
                                        <td>${question.maxResponse ?? 'N/A'}</td>
                                        <td>${question.sequence ?? 'N/A'}</td>
                                        <td>
                                            <label class="switch">
                                                <input type="checkbox" class="toggle-mandatory" data-question-id="${question.id}" ${checked}>
                                                <span class="slider round"></span>
                                            </label>
                                        </td>
                                    </tr>
                                `);
                                    }
                                });
                            } else {
                                $('#questionList').html(
                                    '<tr><td colspan="9">No questions available.</td></tr>');
                            }
                        }
                    });
                }
            });

            $('#questionnaireForm').submit(function(e) {
                e.preventDefault();
                let programme_id = $('#programmeSelect').val();
                if (!programme_id) {
                    alert("Please select a programme first.");
                    return;
                }

                let selectedQuestions = [];
                $('.select-question:checked').each(function() {
                    let question_id = $(this).data('question-id');
                    let mandatory = $(this).closest('tr').find('.toggle-mandatory').is(':checked') ?
                        'Yes' : 'No';

                    selectedQuestions.push({
                        question_id: question_id,
                        mandatory: mandatory
                    });
                });

                if (selectedQuestions.length === 0) {
                    alert("Please select at least one question.");
                    return;
                }

                $.ajax({
                    url: "{{ route('admin.questionnaire_programme.submit') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        programme_id: programme_id,
                        questions: selectedQuestions
                    },
                    success: function(response) {
                        alert(response.message);
                    },
                    error: function(xhr) {
                        alert("An error occurred: " + xhr.responseJSON.message);
                    }
                });
            });
        });
    </script>
@endsection
