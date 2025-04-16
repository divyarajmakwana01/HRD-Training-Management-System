<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use App\Mail\CoordinatorPasswordResetMail;
use Exception;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;



class CoordinatorController extends Controller
{
    // Show the coordinator profile form
    public function profile(Request $request)
    {
        Log::info('Accessing coordinator profile form.');

        if (!session()->has('coordinator_email')) {
            Log::warning('Unauthorized access attempt to coordinator profile page.');
            return redirect()->route('coordinators.login')
                ->withErrors(['message' => 'Please log in first.']);

        }

        $email = session('coordinator_email');
        Log::info("Fetching profile for coordinator: $email");

        $coordinator = DB::selectOne("SELECT * FROM coordinators WHERE email = ?", [$email]);


        // Fetch the login record using the query builder
        $login = DB::table('login')->where('email', $email)->first();

        if (!$login) {
            Log::error("Coordinator with email $email not found in login table.");
            return redirect()->route('coordinators.login')
                ->withErrors(['message' => 'Coordinator not found']);
        }

        // Retrieve the coordinator's profile using the login ID
        $coordinator = DB::table('coordinators')
            ->where('login_id', $login->id)
            ->first();

        Log::info("Fetched coordinator profile for login ID: {$login->id}");

        return view('coordinators.profile', compact('coordinator'));
    }


    public function Dashboard()
    {
        try {
            // Fetch counts for each programme type
            $webinars = DB::select("SELECT COUNT(*) as count FROM programme WHERE programmeType = 1")[0]->count ?? 0;
            $userAwareness = DB::select("SELECT COUNT(*) as count FROM programme WHERE programmeType = 2")[0]->count ?? 0;
            $workshops = DB::select("SELECT COUNT(*) as count FROM programme WHERE programmeType = 3")[0]->count ?? 0;
            $trainings = DB::select("SELECT COUNT(*) as count FROM programme WHERE programmeType = 4")[0]->count ?? 0;
            $collaborative = DB::select("SELECT COUNT(*) as count FROM programme WHERE programmeType = 5")[0]->count ?? 0;
            $participants = DB::select("SELECT COUNT(*) as count FROM participants")[0]->count ?? 0;

            // Debug logs to check if data is fetched
            Log::info("Coordinator Dashboard Data: ", compact(
                'webinars',
                'userAwareness',
                'workshops',
                'trainings',
                'collaborative',
                'participants'
            ));

            // Pass data to the view
            return view('coordinators.dashboard', compact(
                'webinars',
                'userAwareness',
                'workshops',
                'trainings',
                'collaborative',
                'participants'
            ));
        } catch (Exception $e) {
            Log::error("Error fetching programme counts: " . $e->getMessage());
            return redirect()->back()->with('error', 'Error loading data. Check logs.');
        }
    }

    public function showProgrammes()
    {
        if (!session()->has('coordinator_id')) {
            return redirect()->route('coordinators.login')
                ->withErrors(['message' => 'Please log in first.']);
        }

        $coordinatorId = session('coordinator_id');

        $sql = "SELECT DISTINCT p.* 
                FROM coordinators c
                JOIN programme_coordinators pc ON c.id = pc.coordinator_id
                JOIN programme p ON pc.programme_id = p.id
                WHERE c.login_id = :coordinatorId";

        $programmes = collect(DB::select($sql, ['coordinatorId' => $coordinatorId])); // Convert to collection

        return view('coordinators.programmes', compact('programmes'));
    }




    // Store coordinator profile details
    public function store(Request $request)
    {
        $request->validate([
            'login_id' => 'required|integer',
            'name' => 'required|string|max:255',
            'designation' => 'nullable|string|max:255',
            'email' => 'required|email|max:255|unique:coordinators,email',
            'mobile' => 'nullable|string|max:20',
            'contact_no' => 'nullable|string|max:20',
            'division' => 'nullable|string|max:255',
            'facebook' => 'nullable|url|max:255',
            'linkedin' => 'nullable|url|max:255',
            'twitter' => 'nullable|url|max:255',
            'orcid' => 'nullable|string|max:255',
            'biography' => 'nullable|string',
            'active' => 'required|boolean',
        ]);

        DB::table('coordinators')->insert([
            'login_id' => $request->login_id,
            'name' => $request->name,
            'designation' => $request->designation,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'contact_no' => $request->contact_no,
            'division' => $request->division,
            'facebook' => $request->facebook,
            'linkedin' => $request->linkedin,
            'twitter' => $request->twitter,
            'orcid' => $request->orcid,
            'biography' => $request->biography,
            'active' => $request->active,
            'createdby' => auth()->user()->name ?? 'System',
            'updatedby' => auth()->user()->name ?? 'System',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('coordinators.profile')->with('success', 'Coordinator profile saved successfully!');
    }
    public function update(Request $request, $id)
    {
        Log::info("Update request received for coordinator.", ['id' => $id, 'request' => $request->all()]);

        try {
            // Validate incoming data; email is optional
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'designation' => 'nullable|string|max:255',
                'email' => "nullable|email|max:255|unique:coordinators,email,{$id}",
                'mobile' => 'nullable|string|max:20',
                'contact_no' => 'nullable|string|max:20',
                'division' => 'nullable|string|max:255',
                'facebook' => 'nullable|url|max:255',
                'linkedin' => 'nullable|url|max:255',
                'twitter' => 'nullable|url|max:255',
                'orcid' => 'nullable|string|max:255',
                'biography' => 'nullable|string',
            ]);
            Log::info("Validation passed for coordinator update.", $validatedData);

            // Build data array for update
            $dataToUpdate = [
                'name' => $validatedData['name'],
                'designation' => $validatedData['designation'],
                'mobile' => $validatedData['mobile'],
                'contact_no' => $validatedData['contact_no'],
                'division' => $validatedData['division'],
                'facebook' => $validatedData['facebook'],
                'linkedin' => $validatedData['linkedin'],
                'twitter' => $validatedData['twitter'],
                'orcid' => $validatedData['orcid'],
                'biography' => $validatedData['biography'],
                'updated_at' => now(),
            ];

            // Only update email if a non-null value is provided.
            if (!empty($validatedData['email'])) {
                $dataToUpdate['email'] = $validatedData['email'];
            }

            $updated = DB::table('coordinators')
                ->where('id', $id)
                ->update($dataToUpdate);

            if ($updated) {
                Log::info("Coordinator profile updated successfully.", ['id' => $id]);
            } else {
                Log::warning("No rows updated for coordinator.", ['id' => $id]);
            }

            return redirect()->route('coordinators.profile')
                ->with('success', 'Coordinator profile updated successfully!');
        } catch (Exception $e) {
            Log::error("Error updating coordinator profile.", ['id' => $id, 'error' => $e->getMessage()]);
            return redirect()->route('coordinators.profile')
                ->with('error', 'An error occurred while updating the profile.');
        }
    }



    // Show the coordinator login form
    public function showLoginForm()
    {
        return view('coordinators.login'); // Ensure the view file exists
    }

    // Handle coordinator login


    public function login(Request $request)
    {
        Log::info('Coordinator login attempt.', ['email' => $request->email]);

        // Validate input
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'captcha_input' => 'required',
        ]);

        if ((string) $request->captcha_input !== session('captcha')) {
            Log::warning('Login failed: Incorrect CAPTCHA.', ['email' => $request->email]);
            return redirect()->back()
                ->withInput($request->only('email'))
                ->withErrors(['captcha_input' => 'Incorrect CAPTCHA. Please try again.']);
        }

        // Fetch Coordinator from DB
        $coordinator = DB::table('login')->where('email', $request->email)->first();

        // Validate Coordinator Credentials
        if (!$coordinator || !Hash::check($request->password, $coordinator->password)) {
            Log::warning('Login failed: Invalid credentials.', ['email' => $request->email]);
            return redirect()->back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Invalid email or password.']);
        }

        // Check if user has 'A' rights (Coordinator)
        if ($coordinator->rights !== 'A') {
            Log::warning('Login failed: User does not have coordinator rights.', ['email' => $request->email]);
            return redirect()->back()
                ->withErrors(['email' => 'Unauthorized access. Only coordinators can log in.']);
        }

        // ✅ Store Coordinator Session
        session([
            'coordinator_id' => $coordinator->id,
            'coordinator_email' => $coordinator->email,
        ]);

        // ✅ Remove CAPTCHA from session after successful login
        session()->forget('captcha');

        Log::info('Login successful.', ['email' => $coordinator->email]);

        return redirect()->route('coordinators.dashboard')->with('success', 'Login successful.');
    }



    // Show the coordinator registration form
    public function create()
    {
        $coordinators = DB::table('coordinators')->get();
        return view('admin.coordinators.create', compact('coordinators'));
    }

    // Store new coordinator & send activation link
    public function register(Request $request)
    {
        try {
            $request->validate([
                'name' => 'nullable|string|max:255',
                'designation' => 'nullable|string|max:255',
                'email' => 'required|email|unique:login,email',
                'mobile' => 'nullable|string|max:15',
                'contact_no' => 'nullable|string|max:15',
                'division' => 'nullable|string|max:255',
                'facebook' => 'nullable|url',
                'linkedin' => 'nullable|url',
                'twitter' => 'nullable|url',
                'orcid' => 'nullable|string|max:255',
                'biography' => 'nullable|string',

            ]);

            $resetToken = Str::random(60);
            $loginId = DB::table('login')->insertGetId([
                'email' => $request->email,
                'rights' => 'A',
                'active' => false,
                'password_reset_token' => $resetToken,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('coordinators')->insert([
                'login_id' => $loginId,
                'name' => $request->name,
                'designation' => $request->designation,
                'email' => $request->email,
                'mobile' => $request->mobile,
                'contact_no' => $request->contact_no,
                'division' => $request->division,
                'facebook' => $request->facebook,
                'linkedin' => $request->linkedin,
                'twitter' => $request->twitter,
                'orcid' => $request->orcid,
                'biography' => $request->biography,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $resetLink = route('coordinator.reset-password', ['token' => $resetToken]);
            Mail::to($request->email)->send(new CoordinatorPasswordResetMail($resetLink));

            return redirect()->route('admin.coordinators.create')->with('success', 'Coordinator registered. Activation link sent.');

        } catch (Exception $e) {
            return redirect()->route('admin.coordinators.create')->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // Logout coordinator
    public function logout()
    {
        Session::forget('coordinator_id');
        Session::forget('coordinator_email');

        return redirect()->route('coordinators.login')->with('success', 'Logged out successfully.');
    }

    public function index()
    {
        $coordinators = DB::table('coordinators')->get(); // Fetch all coordinators from the database
        return view('admin.coordinators.index', compact('coordinators'));
    }

    public function show($id)
    {
        $coordinator = DB::table('coordinators')->where('id', $id)->first();
        return view('admin.coordinators.show', compact('coordinator'));
    }

    public function edit($id)
    {
        $coordinator = DB::table('coordinators')->where('id', $id)->first();
        return view('admin.coordinators.edit', compact('coordinator'));
    }
    public function updateCoordinators(Request $request, $id)
    {
        DB::table('coordinators')
            ->where('id', $id)
            ->update([
                'name' => $request->name,
                'designation' => $request->designation,
                'mobile' => $request->mobile,
                'division' => $request->division,
                'contact_no' => $request->contact_no,
                'biography' => $request->biography,
                'facebook' => $request->facebook,
                'linkedin' => $request->linkedin,
                'twitter' => $request->twitter,
                'orcid' => $request->orcid,
            ]);

        // Redirect to create page after update
        return redirect()->route('admin.coordinators.create')->with('success', 'Coordinator updated successfully.');
    }


    public function destroy($id)
    {
        DB::table('coordinators')->where('id', $id)->delete();
        return redirect()->route('admin.coordinators.create')->with('success', 'Coordinator deleted successfully');
    }
    public function showResetPasswordForm($token)
    {
        return view('coordinators.reset-password', [
            'token' => $token,
            'is_account_creation' => false
        ]);
    }

    public function activate($token)
    {
        $login = DB::table('login')->where('password_reset_token', $token)->first();

        if (!$login) {
            return redirect()->route('admin.coordinators.create')
                ->with('error', 'Invalid activation link.');
        }

        return view('coordinators.reset-password', ['token' => $token]);
    }
    public function setPassword(Request $request, $token)
    {
        try {
            $request->validate([
                'password' => 'required|min:8|confirmed',
            ]);

            $login = DB::table('login')->where('password_reset_token', $token)->first();

            if (!$login) {
                return redirect()->route('admin.coordinators.create')
                    ->with('error', 'Invalid activation link.');
            }

            DB::table('login')->where('id', $login->id)->update([
                'password' => Hash::make($request->password),
                'password_reset_token' => null,
                'active' => true,
                'updated_at' => now(),
            ]);

            return redirect()->route('coordinators.login')
                ->with('success', 'Account activated successfully!');
        } catch (Exception $e) {
            // Redirect back to the previous page with the error message
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }


    public function fetchRegisteredParticipants()
    {
        // Get login_id from session (which was stored at login)
        $loginId = session('coordinator_id'); // This is actually login.id

        if (!$loginId) {
            return redirect()->route('coordinators.login')->with('error', 'Please log in first.');
        }

        // Get actual coordinator_id from the coordinators table
        $coordinator = DB::table('coordinators')->where('login_id', $loginId)->first();

        if (!$coordinator) {
            return redirect()->route('coordinators.login')->with('error', 'Coordinator not found.');
        }

        // Now use the correct coordinator_id for fetching participants
        $participants = DB::select("
            SELECT DISTINCT 
        participants.id AS participant_id,
        participants.fname,
        participants.lname,
        participants.email,
        participants.mobile,  
        programme.name AS programme_name,
        programme.startdate,
        programme.enddate,
        registration.reg_type,
        registration.accommodation
        FROM registration
        JOIN participants ON registration.participant_id = participants.id
        JOIN programme ON registration.programme_id = programme.id
        JOIN programme_coordinators ON programme.id = programme_coordinators.programme_id
        WHERE programme_coordinators.coordinator_id = ?

        ", [$coordinator->id]);

        return view('coordinators.registered_participants', compact('participants'));
    }

    public function showAssignedProgrammes($id)
    {
        $sql = "SELECT DISTINCT p.* 
                FROM programme_coordinators pc
                JOIN programme p ON pc.programme_id = p.id
                WHERE pc.coordinator_id = :id";

        $programmes = DB::select($sql, ['id' => $id]);

        // Fetch coordinator and get the first record
        $coordinator = DB::select("SELECT * FROM coordinators WHERE id = :id LIMIT 1", ['id' => $id]);
        $coordinator = !empty($coordinator) ? $coordinator[0] : null; // Convert array to object

        return view('admin.coordinators.programmes', compact('programmes', 'coordinator'));
    }
}
