<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class ProgrammeQuestionController extends Controller
{
    // ✅ Display all programme questions
    public function index()
    {
        $questions = DB::select("SELECT * FROM programme_questions ORDER BY id ASC");
        return view('admin.programme_questions.index', compact('questions'));
    }

    // ✅ Show form to add a new question (If using modal, this method is not needed)

    // ✅ Store the new question in the database
    public function store(Request $request)
    {
        $request->validate([
            'questions' => 'required|string|max:255',
            'sub_question' => 'nullable|integer',
            'answerType' => 'required|integer|in:1,2,3,4',
            'answerOption' => 'nullable|string|max:255',
            'answerValidation' => 'nullable|string|max:100',
            'maxResponse' => 'nullable|string|max:100',
            'active' => 'nullable|boolean',
        ]);

        DB::insert("
            INSERT INTO programme_questions 
            (questions, sub_question, answerType, answerOption, answerValidation, maxResponse, active, createdby, created) 
            VALUES (:questions, :sub_question, :answerType, :answerOption, :answerValidation, :maxResponse, :active, :createdby, NOW())
        ", [
            'questions' => $request->questions,
            'sub_question' => $request->sub_question ?? null,
            'answerType' => $request->answerType,
            'answerOption' => $request->answerOption ?? null,
            'answerValidation' => $request->answerValidation ?? null,
            'maxResponse' => $request->maxResponse ?? null,
            'active' => $request->active ?? 1, // Default to active (1)
            'createdby' => 'Admin' // Placeholder for creator, modify if needed
        ]);

        return redirect()->route('admin.programme_questions.index')->with('success', 'Question added successfully!');
    }
    public function toggleStatus(Request $request)
    {
        DB::update("UPDATE programme_questions SET active = ? WHERE id = ?", [
            $request->active,
            $request->id
        ]);

        return response()->json(['message' => 'Status updated successfully!']);
    }



}
