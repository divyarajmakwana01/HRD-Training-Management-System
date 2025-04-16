<?php

namespace App\Http\Controllers\Admin;

use DB;
use Log;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\ParticipantWelcomeMail;
use Illuminate\Validation\ValidationException;

class ParticipantsController extends Controller
{
    public function index(Request $request)
    {
        Log::info('Fetching participant list');

        $states = DB::select('SELECT DISTINCT state_code, state_name FROM state_district');
        $districts = DB::select('SELECT district_code, district_name, state_code FROM state_district');
        $programmes = DB::select("SELECT id, name FROM programme");

        $selectedProgramme = $request->input('programme');

        // Fetch programme details if selected
        $programmeDetails = null;
        if ($selectedProgramme) {
            $programmeDetails = DB::selectOne("SELECT * FROM programme WHERE id = ?", [$selectedProgramme]);
        }

        // Fetch participants **only if a programme is selected**
        $participants = [];
        if ($selectedProgramme) {
            $participants = DB::select("
            SELECT 
                p.id AS participant_id, p.prefix, p.fname, p.lname, p.designation, 
                p.gender, p.institute_name, p.address, p.city, p.pincode, 
                p.district, sd.district_name, p.state, sd.state_name, p.country, 
                p.mobile, p.email, p.facebook, p.linkedin, p.twitter, p.orcid, p.biography,
                r.reg_type, r.reg_group_name, r.category, r.accommodation, r.acc_cat, 
                r.payment, r.pno, r.pbank, r.pdate, r.pamt, r.payment_remarks, 
                r.payment_verification, r.nation, r.adate, r.rdate, r.mode, 
                r.programme_id,
                prog.name AS programme_name, prog.startdate, prog.enddate, 
                l.email AS login_email, l.rights AS login_rights
            FROM participants p
            LEFT JOIN registration r ON p.id = r.participant_id
            LEFT JOIN programme prog ON r.programme_id = prog.id
            LEFT JOIN login l ON p.login_id = l.id
            LEFT JOIN state_district sd ON sd.district_code = p.district
            WHERE (r.active != 0 OR r.active IS NULL)
            AND r.programme_id = ?
        ", [$selectedProgramme]);
        }

        // Fetch responses **only if participants exist**
        $groupedResponses = [];
        if (!empty($participants)) {
            $responses = DB::select("
            SELECT 
                pr.participant_id,
                pr.id AS response_id, 
                pr.question_id, 
                pq.questions AS question_text,  
                pr.response, 
                pr.active 
            FROM programme_responses pr
            LEFT JOIN programme_questions pq ON pr.question_id = pq.id
            WHERE pr.participant_id IN (" . implode(',', array_column($participants, 'participant_id')) . ")
        ");

            // Group responses under each participant
            foreach ($responses as $response) {
                $groupedResponses[$response->participant_id][] = $response;
            }

            // Attach responses to each participant
            foreach ($participants as $participant) {
                $participant->responses = $groupedResponses[$participant->participant_id] ?? [];
            }
        }

        return view('admin.create_participant', compact('participants', 'states', 'districts', 'programmes', 'selectedProgramme', 'programmeDetails'));
    }



    // Store a new participant
    public function store(Request $request)
    {
        try {
            // Validate input
            $validatedData = $request->validate([
                'reg_type' => 'required|integer',
                'programme_id' => 'required|integer',
                'prefix' => 'required|string',
                'fname' => 'required|string',
                'lname' => 'required|string',
                'gender' => 'required|string',
                'designation' => 'required|string',
                'institute_name' => 'required|string',
                'country' => 'required|string',
                'city' => 'required|string',
                'state_code' => 'required|integer',
                'district' => 'required|integer',
                'pincode' => 'required|digits:6',
                'mobile' => 'required|digits:10',
                'email' => 'required|email|unique:login,email',
                'payment' => 'required|boolean',
                'payment_verification' => 'required|integer|in:0,1,2,3',
                'mode' => 'required|string',
            ]);

            Log::info('Validated Data:', $validatedData);

            // Check if email already exists
            $existingLogin = DB::table('login')->where('email', $validatedData['email'])->first();
            if ($existingLogin) {
                return redirect()->back()->with('error', 'This email is already registered.');
            }

            // Generate a secure random password
            $randomPassword = bin2hex(random_bytes(5)); // 10-character random password

            // Insert into the login table
            $loginId = DB::table('login')->insertGetId([
                'email' => $validatedData['email'],
                'password' => Hash::make($randomPassword), // Store hashed password
                'rights' => 'U',
            ]);

            // Insert participant
            $participantId = DB::table('participants')->insertGetId([
                'prefix' => $validatedData['prefix'],
                'fname' => $validatedData['fname'],
                'lname' => $validatedData['lname'],
                'gender' => $validatedData['gender'],
                'designation' => $validatedData['designation'],
                'institute_name' => $validatedData['institute_name'],
                'country' => $validatedData['country'],
                'address' => $request->address,
                'city' => $validatedData['city'],
                'state' => $validatedData['state_code'],
                'district' => $validatedData['district'],
                'pincode' => $validatedData['pincode'],
                'mobile' => $validatedData['mobile'],
                'email' => $validatedData['email'],
                'login_id' => $loginId,
            ]);

            Log::info("Inserted participant ID: {$participantId}");

            // Insert registration
            DB::table('registration')->insert([
                'participant_id' => $participantId,
                'reg_type' => $validatedData['reg_type'],
                'programme_id' => $validatedData['programme_id'],
                'payment' => $validatedData['payment'],
                'payment_verification' => $validatedData['payment_verification'],
                'mode' => $validatedData['mode'],
                'active' => 1,
            ]);

            Log::info("Inserted registration for participant ID: {$participantId}");

            // Prepare email data
            $emailData = [
                'name' => $validatedData['fname'] . ' ' . $validatedData['lname'],
                'email' => $validatedData['email'],
                'programme' => DB::table('programme')->where('id', $validatedData['programme_id'])->value('name'),
                'password' => $randomPassword, // Send plain password
            ];

            try {
                Log::info("Preparing to send email to: " . $validatedData['email']);

                Mail::to($validatedData['email'])->send(new ParticipantWelcomeMail($emailData));

                Log::info("Email sent successfully to: " . $validatedData['email']);
            } catch (\Exception $e) {
                Log::error("Mail sending failed: " . $e->getMessage());
                return redirect()->back()->with('error', 'Participant registered, but email could not be sent.');
            }

            return redirect()->back()->with('success', 'Participant registered successfully.');
        } catch (\Exception $e) {
            Log::error("Error in participant registration: " . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while registering the participant.');
        }
    }

    // Update participant details
    public function update(Request $request, $id)
    {
        Log::info('Updating participant', ['participant_id' => $id, 'request' => $request->all()]);

        // Update participant details
        DB::update("UPDATE participants SET prefix = ?, fname = ?, lname = ?, designation = ?, gender = ?, institute_name = ?, address = ?, city = ?, district = ?, state = ?, country = ?, pincode = ?, mobile = ?, email = ?, facebook = ?, linkedin = ?, twitter = ?, orcid = ?, biography = ? WHERE id = ?", [
            $request->prefix,
            $request->fname,
            $request->lname,
            $request->designation,
            $request->gender,
            $request->institute_name,
            $request->address,
            $request->city,
            $request->district,
            $request->state,
            $request->country,
            $request->pincode,
            $request->mobile,
            $request->email,
            $request->facebook,
            $request->linkedin,
            $request->twitter,
            $request->orcid,
            $request->biography,
            $id
        ]);

        Log::info('Updated participant record', ['participant_id' => $id]);

        // Check if registration exists for this participant and programme
        $existingRegistration = DB::table('registration')
            ->where('participant_id', $id)
            ->where('programme_id', $request->programme_id)
            ->first();

        if ($existingRegistration) {
            // Update existing registration
            DB::update("UPDATE registration SET reg_type = ?, payment = ?, payment_verification = ?, mode = ?, active = ? WHERE participant_id = ? AND programme_id = ?", [
                $request->reg_type,
                $request->payment,
                $request->payment_verification,
                $request->mode,
                1,  // Assuming active is always 1
                $id,
                $request->programme_id
            ]);

            Log::info('Updated existing registration record', ['participant_id' => $id, 'programme_id' => $request->programme_id]);
        } else {
            // Optional: Decide whether to insert a new registration if none exists
            Log::warning("No existing registration found for participant_id: $id and programme_id: {$request->programme_id}. No new entry created.");
        }

        return redirect()->back()->with('success', 'Participant updated successfully');
    }


    public function verifyResponse($participant_id)
    {
        try {
            // Start a database transaction
            DB::beginTransaction();

            // Check if the participant has any pending responses (active = 1)
            $pendingResponses = DB::select("SELECT id FROM programme_responses WHERE participant_id = ? AND active = 1", [$participant_id]);

            if (!$pendingResponses) {
                return redirect()->back()->with('error', 'No pending responses found for this participant.');
            }

            // Log verification attempt
            Log::info("Verifying all responses for participant ID: " . $participant_id);

            // Update all responses for this participant to active = 2 (Verified)
            $updated = DB::update("UPDATE programme_responses SET active = 2 WHERE participant_id = ?", [$participant_id]);

            // Check if the update was successful
            if ($updated) {
                DB::commit();
                return redirect()->back()->with('success', 'All responses verified successfully.');
            } else {
                DB::rollBack();
                return redirect()->back()->with('error', 'Failed to verify responses.');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error verifying responses for participant ID: " . $participant_id . " - " . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while verifying the responses.');
        }
    }



}
