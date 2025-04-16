<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log; // Import Log Facade
use Barryvdh\DomPDF\Facade\Pdf;

class ParticipantsController extends Controller
{
    /**
     * Show the participant profile form.
     */
    public function showProfile(Request $request)
    {
        Log::info('Accessing participant profile form.');

        if (!session()->has('user_email')) {
            Log::warning('Unauthorized access attempt to profile page.');
            return redirect()->route('participants.login')
                ->withErrors(['message' => 'Please log in first.']);
        }

        $email = session('user_email');
        Log::info("Fetching profile for user: $email");

        // Get login ID from email
        $login = DB::select('SELECT id FROM login WHERE email = ?', [$email]);

        if (!$login) {
            Log::error("User with email $email not found in login table.");
            return redirect()->route('participants.login')
                ->withErrors(['message' => 'User not found']);
        }

        $participant = DB::table('participants')
            ->where('login_id', $login[0]->id)
            ->first();

        Log::info("Fetched participant profile for login ID: {$login[0]->id}");

        $states = DB::select('SELECT DISTINCT state_code, state_name FROM state_district');
        $districts = DB::select('SELECT * FROM state_district');

        return view('participants.profile', compact('states', 'districts', 'participant'));
    }

    /**
     * Store or update participant profile.
     */
    public function storeProfile(Request $request)
    {
        Log::info('Profile update request received.');

        if (!session()->has('user_email')) {
            Log::warning('Unauthorized profile update attempt.');
            return redirect()->route('participants.login')
                ->withErrors(['message' => 'Please log in first.']);
        }

        $email = session('user_email');
        Log::info("Updating profile for user: $email");

        // Get login ID from email
        $login = DB::select('SELECT id FROM login WHERE email = ?', [$email]);

        if (!$login) {
            Log::error("User with email $email not found.");
            return redirect()->route('participants.profile')
                ->with('error', 'User not found');
        }

        $data = [
            'login_id' => $login[0]->id,
            'prefix' => $request->input('prefix'),
            'fname' => $request->input('fname'),
            'lname' => $request->input('lname'),
            'designation' => $request->input('designation'),
            'gender' => $request->input('gender'),
            'institute_name' => $request->input('institute_name'),
            'address' => $request->input('address'),
            'city' => $request->input('city'),
            'pincode' => $request->input('pincode'),
            'state' => $request->input('state_code'),
            'district' => $request->input('district_code'),
            'country' => $request->input('country'),
            'mobile' => $request->input('mobile'),
            'email' => $request->input('email'),
            'facebook' => $request->input('facebook'),
            'linkedin' => $request->input('linkedin'),
            'twitter' => $request->input('twitter'),
            'orcid' => $request->input('orcid'),
            'biography' => $request->input('biography'),
        ];

        // Handle Profile Image Upload
        if ($request->input('remove_image')) {
            Log::info("Removing profile image for user: $email");

            $existing = DB::table('participants')
                ->where('login_id', $login[0]->id)
                ->first();

            if ($existing && $existing->image) {
                Storage::delete('public/' . $existing->image);
                Log::info("Deleted profile image: {$existing->image}");
            }

            $data['image'] = null;
        } elseif ($request->hasFile('image')) {
            Log::info("Uploading new profile image for user: $email");

            $data['image'] = $request->file('image')->store('profile_images', 'public');

            // Remove old image
            $existing = DB::table('participants')
                ->where('login_id', $login[0]->id)
                ->first();

            if ($existing && $existing->image) {
                Storage::delete('public/' . $existing->image);
                Log::info("Deleted old profile image: {$existing->image}");
            }
        }

        // Check if user exists
        $existing = DB::table('participants')
            ->where('login_id', $login[0]->id)
            ->first();

        if ($existing) {
            DB::table('participants')
                ->where('login_id', $login[0]->id)
                ->update($data);

            Log::info("Updated profile for user: $email");
        } else {
            DB::table('participants')->insert($data);

            Log::info("Created new profile for user: $email");
        }

        return redirect()->route('participants.profile')
            ->with('success', 'Profile saved successfully');
    }

    /**
     * Show all available programmes.
     */
    public function showProgramme()
    {
        Log::info("Fetching all active programmes.");

        // Get logged-in participant
        $email = session('user_email');
        $participant = DB::table('participants')->where('email', $email)->first();

        $states = DB::select('SELECT DISTINCT state_code, state_name FROM state_district');
        $districts = DB::select('SELECT district_code, district_name, state_code FROM state_district');

        // Fetch active programmes
        $programmes = DB::select("SELECT * FROM programme WHERE active = 1");

        // Fetch participant registrations
        $registration = [];
        if ($participant) {
            $registrations = DB::select("SELECT * FROM registration WHERE participant_id = ?", [$participant->id]);

            foreach ($registrations as $reg) {
                $registration[$reg->programme_id] = $reg;
            }
        }

        // Fetch all programme questions along with answer options
        $questions = DB::select("
            SELECT pq.programme_id, q.id, q.questions, q.answerType, q.answerOption
            FROM programme_questionnaire pq
            JOIN programme_questions q ON pq.question_id = q.id
        ");

        // Group questions by programme_id
        $programmeQuestions = [];
        foreach ($questions as $question) {
            $programmeQuestions[$question->programme_id][] = $question;
        }

        $userResponses = [];
        if ($participant) {
            $responses = DB::select("
        SELECT programme_id, question_id, response, active 
        FROM programme_responses 
        WHERE participant_id = ?
    ", [$participant->id]);

            foreach ($responses as $response) {
                $userResponses[$response->programme_id][$response->question_id] = $response->response;
                $userResponses[$response->programme_id][$response->question_id . '_active'] = $response->active;
            }
        }


        Log::info("Data fetched successfully", [
            'programmes' => count($programmes),
            'registrations' => count($registration),
            'questions' => count($questions),
            'responses' => count($userResponses),
        ]);

        return view('participants.programme', compact('programmes', 'participant', 'states', 'districts', 'registration', 'programmeQuestions', 'userResponses'));
    }


    public function submitResponse(Request $request)
    {
        $email = session('user_email');
        $participant = DB::table('participants')->where('email', $email)->first();

        if (!$participant) {
            return redirect()->back()->with('error', 'Session expired. Please log in again.');
        }

        $participant_id = $participant->id;

        foreach ($request->responses as $question_id => $response) {
            // Convert checkbox responses into a comma-separated string
            if (is_array($response)) {
                $response = implode(', ', $response);
            }

            // Use updateOrInsert to update if exists, insert if not
            DB::table('programme_responses')->updateOrInsert(
                [
                    'programme_id' => $request->programme_id,
                    'question_id' => $question_id,
                    'participant_id' => $participant_id,
                ],
                [
                    'response' => $response,
                    'active' => 1,
                    'updatedby' => $participant_id,
                    'updated' => now(),
                    'createdby' => $participant_id, // Keep original creator
                    'created' => now(), // Keep original creation time if exists
                ]
            );
        }

        return redirect()->back()->with('success', 'Responses submitted successfully.');
    }



    public function toggleStatus($id)
    {
        Log::info("Toggling active status for programme ID: $id");

        // Get the programme
        $programme = DB::table('programme')->where('id', $id)->first();

        if (!$programme) {
            Log::error("Programme with ID $id not found.");
            return redirect()->route('admin.programme')->with('error', 'Programme not found');
        }

        // Toggle the active status
        $newStatus = $programme->active == 1 ? 0 : 1;

        DB::table('programme')
            ->where('id', $id)
            ->update(['active' => $newStatus]);

        Log::info("Programme ID $id active status updated to $newStatus.");

        // Redirect back to the admin programme page
        return redirect()->route('admin.programme');
    }
    public function registerForProgramme(Request $request, $programme_id)
    {
        Log::info("Processing registration for Programme ID: $programme_id");

        // Fetch participant using session email
        $email = session('user_email');
        $participant = DB::table('participants')->where('email', $email)->first();

        if (!$participant) {
            // Insert new participant
            $participant_id = DB::table('participants')->insertGetId([
                'prefix' => $request->input('prefix'),
                'fname' => $request->input('first_name'),
                'lname' => $request->input('last_name'),
                'designation' => $request->input('designation'),
                'gender' => $request->input('gender'),
                'institute_name' => $request->input('institute_name'),
                'address' => $request->input('address'),
                'city' => $request->input('city'),
                'pincode' => $request->input('pincode'),
                'district' => $request->input('district'),
                'state' => $request->input('state'),
                'country' => $request->input('country'),
                'mobile' => $request->input('mobile'),
                'email' => $email,
                'facebook' => $request->input('facebook'),
                'linkedin' => $request->input('linkedin'),
                'twitter' => $request->input('twitter'),
                'orcid' => $request->input('orcid'),
                'biography' => $request->input('biography'),
                'active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
                'createdby' => $email,
                'updatedby' => $email,
            ]);
            Log::info("New participant created with ID: $participant_id");
        } else {
            // Update existing participant
            $participant_id = $participant->id;
            DB::table('participants')->where('id', $participant_id)->update([
                'prefix' => $request->input('prefix'),
                'fname' => $request->input('first_name'),
                'lname' => $request->input('last_name'),
                'designation' => $request->input('designation'),
                'gender' => $request->input('gender'),
                'institute_name' => $request->input('institute_name'),
                'address' => $request->input('address'),
                'city' => $request->input('city'),
                'pincode' => $request->input('pincode'),
                'district' => $request->input('district'),
                'state' => $request->input('state'),
                'country' => $request->input('country'),
                'mobile' => $request->input('mobile'),
                'facebook' => $request->input('facebook'),
                'linkedin' => $request->input('linkedin'),
                'twitter' => $request->input('twitter'),
                'orcid' => $request->input('orcid'),
                'biography' => $request->input('biography'),
                'updated_at' => now(),
                'updatedby' => $email,
            ]);
            Log::info("Existing participant updated: ID $participant_id");
        }

        // Check if already registered
        $existingRegistration = DB::table('registration')
            ->where('programme_id', $programme_id)
            ->where('participant_id', $participant_id)
            ->first();

        if ($existingRegistration) {
            // Update existing registration
            DB::table('registration')
                ->where('id', $existingRegistration->id)
                ->update([
                    'accommodation' => $request->input('accommodation'),
                    'category' => $request->input('category'),
                    'reg_type' => $request->input('reg_type'), // 1 for Individual, 2 for Group
                    'reg_group_name' => $request->input('reg_group_name'),
                    'passno' => $request->input('passno'),
                    'passv' => $request->input('passv'),
                    'pob' => $request->input('pob'),
                    'nation' => $request->input('nation'),
                    'updated_at' => now(),
                    'updatedby' => $email,
                ]);
            Log::info("Updated registration for Participant ID: $participant_id in Programme ID: $programme_id");

            return redirect()->back()->with('success', 'Registration updated successfully.');
        } else {
            // Insert new registration only if no existing record is found
            DB::table('registration')->insert([
                'programme_id' => $programme_id,
                'participant_id' => $participant_id,
                'reg_type' => $request->input('reg_type'), // 1 for Individual, 2 for Group
                'reg_group_name' => $request->input('reg_group_name'),
                'accommodation' => $request->input('accommodation'),
                'category' => $request->input('category'),
                'passno' => $request->input('passno'),
                'passv' => $request->input('passv'),
                'pob' => $request->input('pob'),
                'nation' => $request->input('nation'),
                'created_at' => now(),
                'updated_at' => now(),
                'createdby' => $email,
            ]);
            Log::info("New registration created for Participant ID: $participant_id in Programme ID: $programme_id");

            return redirect()->back()->with('success', 'Registration completed successfully.');
        }
    }
    // Get Payment Details
    public function getPaymentDetails($programme_id)
    {
        $email = session('user_email');
        $participant = DB::table('participants')->where('email', $email)->first();

        if (!$participant) {
            return redirect()->back()->with('error', 'Participant not found.');
        }

        $registration = DB::table('registration')
            ->where('programme_id', $programme_id)
            ->where('participant_id', $participant->id)
            ->first();

        return view('participants.modals.payment', compact('registration', 'programme_id'));
    }

    // Store Payment Details
    public function storePaymentDetails(Request $request, $programme_id)
    {
        $email = session('user_email');
        $participant = DB::table('participants')->where('email', $email)->first();

        if (!$participant) {
            return redirect()->back()->with('error', 'Participant not found.');
        }

        DB::table('registration')->where('programme_id', $programme_id)->where('participant_id', $participant->id)->update([
            'pno' => $request->input('pno'),
            'pbank' => $request->input('pbank'),
            'pdate' => $request->input('pdate'),
            'pamt' => $request->input('pamt'),
            'payment' => $request->input('payment'),
            'payment_verification' => '0', // Unverified initially
            'updated_at' => now(),
            'updatedby' => $email,
        ]);

        return redirect()->back()->with('success', 'Payment details updated successfully.');
    }

    // Get Transport Details
    public function getTransportDetails($programme_id)
    {
        $email = session('user_email');
        $participant = DB::table('participants')->where('email', $email)->first();

        if (!$participant) {
            return redirect()->back()->with('error', 'Participant not found.');
        }

        $registration = DB::table('registration')
            ->where('programme_id', $programme_id)
            ->where('participant_id', $participant->id)
            ->first();

        return view('participants.modals.transport', compact('registration', 'programme_id'));
    }

    // Store Transport Details
    public function storeTransportDetails(Request $request, $programme_id)
    {

        $email = session('user_email');

        $participant = DB::table('participants')->where('email', $email)->first();

        if (!$participant) {
            return redirect()->back()->with('error', 'Participant not found.');
        }

        // Validate incoming request data
        $validated = $request->validate([
            'mode' => 'required|string|max:50',
            'adate' => 'required|date',
            'rdate' => 'required|date',
        ]);

        // Update the transport details in the 'registration' table
        DB::table('registration')->where('programme_id', $programme_id)
            ->where('participant_id', $participant->id)
            ->update([
                'mode' => $validated['mode'],
                'adate' => $validated['adate'],
                'rdate' => $validated['rdate'],
                'updated_at' => now(),
                'updatedby' => $email,
            ]);


        return redirect()->back()->with('success', 'Transport details updated successfully.');
    }


    public function generatePDF($programme_id, $action = 'preview')
    {
        // Get participant information from session email
        $email = session('user_email');
        $participant = DB::table('participants')->where('email', $email)->first();

        if (!$participant) {
            return redirect()->back()->with('error', 'Participant not found.');
        }

        // Get registration details
        $registration = DB::table('registration')->where('programme_id', $programme_id)
            ->where('participant_id', $participant->id)->first();

        // Fetch payment and transport details (if stored separately, adjust queries)
        $payment = DB::table('registration')->where('programme_id', $programme_id)
            ->where('participant_id', $participant->id)->first();

        $transport = DB::table('registration')->where('programme_id', $programme_id)
            ->where('participant_id', $participant->id)->first();

        // Combine data for PDF
        $data = compact('participant', 'registration', 'payment', 'transport');

        // Load the view
        $pdf = PDF::loadView('participants.pdf.registration', $data);

        if ($action === 'download') {
            // Direct download
            return $pdf->download('participant_registration_details.pdf');
        }

        // Preview in browser
        return $pdf->stream('participant_registration_details.pdf');
    }

}

