<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

use App\Exports\ParticipantsExport;
use Maatwebsite\Excel\Facades\Excel;

use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    // ✅ Export Participants to Excel
    public function exportParticipants(Request $request)
    {
        $request->validate([
            'programme_id' => 'required|exists:programme,id'
        ]);

        $programmeId = $request->input('programme_id');

        // Fetch Programme Name
        $programme = DB::table('programme')->where('id', $programmeId)->first();
        $programmeName = $programme ? str_replace(' ', '_', $programme->name) : 'programme';

        return Excel::download(new ParticipantsExport($programmeId), "{$programmeName}_participants_list.xlsx");
    }

    public function index()
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
            Log::info("Admin Dashboard Data: ", compact(
                'webinars',
                'userAwareness',
                'workshops',
                'trainings',
                'collaborative',
                'participants'
            ));

            // Pass data to the view
            return view('admin.dashboard', compact(
                'webinars',
                'userAwareness',
                'workshops',
                'trainings',
                'collaborative',
                'participants'
            ));
        } catch (\Exception $e) {
            Log::error("Error fetching programme counts: " . $e->getMessage());
            return redirect()->back()->with('error', 'Error loading data. Check logs.');
        }
    }

    public function showLoginForm()
    {
        return view('admin.login');
    }

    public function programme()
    {
        return view('admin.programme');
    }

    public function login(Request $request)
    {
        // Log login attempt
        Log::info('Admin login attempt.', ['username' => $request->username]);

        // Validate input fields
        $request->validate([
            'username' => 'required|email',
            'password' => 'required',
            'captcha_input' => 'required',
        ]);

        // Log entered CAPTCHA vs stored CAPTCHA
        Log::info('User entered CAPTCHA answer: ' . $request->captcha_input);
        Log::info('Stored CAPTCHA answer in session: ' . session('captcha'));

        // ✅ CAPTCHA Validation (case-sensitive)
        if ((string) $request->captcha_input !== session('captcha')) {
            Log::warning('Admin login failed: Incorrect CAPTCHA.', ['username' => $request->username]);

            return redirect()->route('admin.login')
                ->withInput($request->only('username'))
                ->withErrors(['captcha' => 'Incorrect CAPTCHA. Please try again.']);
        }

        // Fetch admin user from database
        $admin = DB::table('login')->where('email', $request->username)->where('active', 1)->first();

        // Check if admin exists and password is correct
        if (!$admin || !Hash::check($request->password, $admin->password)) {
            Log::warning('Admin login failed: Invalid credentials.', ['username' => $request->username]);

            return redirect()->route('admin.login')
                ->withInput($request->only('username'))
                ->withErrors(['credentials' => 'Invalid email or password.']);
        }

        // ✅ Store admin session
        session([
            'admin_logged_in' => true,
            'admin_id' => $admin->id,
            'admin_username' => $admin->email,
            'admin_rights' => $admin->rights,
        ]);

        // ✅ Remove CAPTCHA from session after successful login
        Session::forget('captcha');

        Log::info('Admin login successful.', ['username' => $admin->email]);

        // Redirect based on user rights (SA → Admin Dashboard, Others → Sample Page)
        return redirect()->route($admin->rights === 'SA' ? 'admin.dashboard' : 'admin.sample');
    }




    public function dashboard()
    {
        // ✅ Check session before allowing access
        if (!Session::has('admin_logged_in')) {
            return redirect()->route('admin.login')->with('message', 'Unauthorized Access');
        }

        return view('admin.dashboard');
    }

    public function samplePage()
    {
        if (!Session::has('admin_logged_in')) {
            return redirect()->route('admin.login')->with('message', 'Unauthorized Access');
        }

        return view('admin.sample');
    }

    public function logout()
    {
        // ✅ Clear session on logout
        Session::flush();
        return redirect()->route('admin.login');
    }

    public function deleteParticipant($id)
    {
        // Set registration as inactive
        DB::table('registration')->where('participant_id', $id)->update(['active' => 0]);

        return redirect()->route('admin.participants')->with('success', 'Participant removed from view.');
    }
}
