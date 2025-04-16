<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Str;

class LoginController extends Controller
{

    public function showIndex()
    {
        return view('index');
    }

    public function showCreateAccountForm()
    {
        return view('participants.create_account');
    }

    public function createAccount(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:login,email',
        ]);

        $token = Str::random(60);
        $expiryTime = Carbon::now()->addMinutes(60);

        DB::table('login')->insert([
            'email' => $request->email,
            'password_reset_token' => $token,
            'token_expiry' => $expiryTime,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        // Send activation link via email
        Mail::send('emails.activation', ['token' => $token], function ($message) use ($request) {
            $message->to($request->email);
            $message->subject('Activation Link');
        });

        return redirect()->back()->with('message', 'Registration link sent to your email.');
    }

    public function showResetPasswordForm($token)
    {
        $user = DB::table('login')
            ->where('password_reset_token', $token)
            ->where('token_expiry', '>=', Carbon::now())
            ->first();

        if (!$user) {
            return redirect('/participants/login')->withErrors(['message' => 'Invalid or expired token']);
        }

        // Determine if this is an account creation or a password reset
        $is_account_creation = is_null($user->password);

        return view('participants.reset_password', [
            'token' => $token,
            'is_account_creation' => $is_account_creation
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'password' => [
                'required',
                'confirmed',
                'min:8',
                'regex:/[A-Z]/',      // must contain at least one uppercase letter
                'regex:/[a-z]/',      // must contain at least one lowercase letter
                'regex:/[0-9]/',      // must contain at least one digit
                'regex:/[@$!%*?&#]/'   // must contain a special character
            ],
        ]);

        $user = DB::table('login')
            ->where('password_reset_token', $request->token)
            ->where('token_expiry', '>=', Carbon::now())
            ->first();

        if (!$user) {
            return redirect('/participants/login')->withErrors(['message' => 'Invalid or expired token']);
        }

        DB::table('login')->where('id', $user->id)->update([
            'password' => Hash::make($request->password),
            'password_reset_token' => null,
            'token_expiry' => null,
            'updated_at' => Carbon::now()
        ]);

        return redirect('/participants/login')->with('message', 'Password has been reset.');
    }
    public function showDashboard()
    {
        return view('participants.dashboard');
    }

    public function showLoginForm()
    {
        return view('participants.login');
    }

    public function storeCaptcha(Request $request)
    {
        \Log::info('Request payload: ', $request->all());
        \Log::info('Before storing CAPTCHA, session data: ', session()->all());

        session(['captcha' => (string) $request->captcha]);

        \Log::info('Storing CAPTCHA in session: ' . $request->captcha);
        \Log::info('After storing CAPTCHA, session data: ', session()->all());

        return response()->json(['status' => 'success']);
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|email',
            'password' => 'required',
            'captcha_input' => 'required',
        ]);

        \Log::info('User entered CAPTCHA answer: ' . $request->captcha_input);
        \Log::info('Stored CAPTCHA answer in session: ' . session('captcha'));

        // Check CAPTCHA
        if ((string) $request->captcha_input !== session('captcha')) {
            \Log::info('Invalid captcha');
            return redirect()->back()
                ->withInput($request->only('username'))
                ->withErrors(['captcha' => 'Incorrect CAPTCHA. Please try again.']);
        }

        $user = DB::table('login')->where('email', $request->username)->first();

        // Check if user exists and password is correct
        if (!$user || !Hash::check($request->password, $user->password)) {
            \Log::info('Invalid email or password');
            return redirect()->back()
                ->withInput($request->only('username'))
                ->withErrors(['credentials' => 'Invalid email or password']);
        }

        // Check if user has 'U' rights (Participant)
        if ($user->rights !== 'U') {
            \Log::warning('Login failed: User does not have participant rights.', ['email' => $request->username]);
            return redirect()->back()
                ->withInput($request->only('username'))
                ->withErrors(['credentials' => 'Unauthorized access. Only participants can log in.']);
        }

        // Store the user’s ID and email in session
        session(['user_id' => $user->id, 'user_email' => $user->email]);

        \Log::info('User authenticated successfully', ['email' => $user->email]);

        return redirect()->intended('/participants/dashboard');
    }


    public function showResetPasswordRequestForm()
    {
        return view('participants.password_request');
    }

    public function sendResetPasswordEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:login,email',
        ]);

        $token = Str::random(60);
        $expiryTime = Carbon::now()->addMinutes(60);

        DB::table('login')->where('email', $request->email)->update([
            'password_reset_token' => $token,
            'token_expiry' => $expiryTime,
            'updated_at' => Carbon::now()
        ]);

        // Send reset link via email
        Mail::send('emails.reset_password', ['token' => $token], function ($message) use ($request) {
            $message->to($request->email);
            $message->subject('Reset Password Link');
        });

        return redirect()->back()->with('message', 'Password reset link sent to your email.');
    }

    public function logout()
    {
        // Clear all session data
        session()->flush();
        return redirect('/participants/login');
    }
}
