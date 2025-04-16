<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuestionnaireProgrammeController extends Controller
{
    public function index()
    {
        $programmes = DB::select("SELECT id, name FROM programme WHERE active = 1 ORDER BY name ASC");
        return view('admin.questionnaire_programme.index', compact('programmes'));
    }

    public function fetchQuestions(Request $request)
    {
        $programme_id = $request->programme_id;

        $questions = DB::select("
           SELECT pq.*, 
        CASE 
        WHEN pqn.programme_id IS NOT NULL THEN 1 
        ELSE 0 
        END AS assigned,
        COALESCE(pqn.mandatory, 'No') AS mandatory
        FROM programme_questions pq
        LEFT JOIN programme_questionnaire pqn 
        ON pq.id = pqn.question_id AND pqn.programme_id = ?
        WHERE pq.active = 1
        ORDER BY pq.id DESC;

        ", [$programme_id]);

        foreach ($questions as $q) {
            switch ($q->answerType) {
                case 1:
                    $q->answerTypeText = 'Checkbox';
                    break;
                case 2:
                    $q->answerTypeText = 'Radio Button';
                    break;
                case 3:
                    $q->answerTypeText = 'Text';
                    break;
                case 4:
                    $q->answerTypeText = 'Likert Scale';
                    break;
                default:
                    $q->answerTypeText = 'Unknown';
                    break;
            }
        }

        return response()->json(['questions' => $questions]);
    }

    public function submitQuestionnaire(Request $request)
    {
        try {
            $programme_id = $request->programme_id;
            $questions = $request->questions; // Array of selected question IDs and mandatory status

            if (!$programme_id || empty($questions)) {
                return response()->json(['message' => 'Programme and questions are required.'], 400);
            }

            foreach ($questions as $question) {
                $mandatory = isset($question['mandatory']) && ($question['mandatory'] == 'Yes' || $question['mandatory'] == 1) ? 1 : 'No';

                DB::table('programme_questionnaire')->updateOrInsert(
                    [
                        'programme_id' => $programme_id,
                        'question_id' => $question['question_id']
                    ],
                    [
                        'mandatory' => $mandatory,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]
                );
            }

            return response()->json(['message' => 'Questionnaire updated successfully.'], 200);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }
}
