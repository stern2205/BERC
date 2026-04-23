<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\LoginLog;
use App\Mail\VerifCode;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\Password;
use App\Mail\VerifCode as VerificationCodeMail;

class AuthController extends Controller
{
    /**
     * =========================================================
     * MODULE 1: LANDING & AUTH NAVIGATION
     * =========================================================
     * Handles basic page routing (landing, login, signup views)
     */

    // Landing page
    public function index()
    {
        return view('landing');
    }

    // Show login page
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Show signup page
    public function showSignupForm()
    {
        return view('auth.signup');
    }


    /**
     * =========================================================
     * MODULE 2: LOGIN SYSTEM
     * =========================================================
     * Handles authentication and login tracking
     */

    public function login(Request $request)
    {
        // Extract credentials
        $credentials = $request->only('email', 'password');

        // Attempt login
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            /**
             * SUBMODULE: LOGIN LOGGING
             * -----------------------------------------
             * Connected to: LoginLog model
             * Purpose: Track user login activity
             */
            LoginLog::create([
                'user_id' => Auth::id(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'logged_in_at' => now(),
            ]);

            return redirect()->route('dashboard');
        }

        // Failed login
        return back()->withErrors([
            'email' => 'Invalid credentials',
        ]);
    }


    /**
     * =========================================================
     * MODULE 3: SIGNUP + EMAIL VERIFICATION
     * =========================================================
     * Flow:
     * signup() → send code → session storage → verifyCode()
     */

    public function signup(Request $request)
    {
        /**
         * SUBMODULE: INPUT VALIDATION
         */
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users|ends_with:@gmail.com,@g.batstate-u.edu.ph,@yahoo.com',
            'password' => 'required|string|min:8|confirmed|regex:/[a-z]/|regex:/[A-Z]/|regex:/[0-9]/|regex:/[@$!%*#?&]/',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        /**
         * SUBMODULE: TEMP FILE STORAGE
         * -----------------------------------------
         * Stores uploaded image temporarily before verification
         */
        $tempPath = null;
        if ($request->hasFile('profile_image')) {
            $file = $request->file('profile_image');
            $filename = time() . '_' . $file->getClientOriginalName();

            $tempDir = public_path('temp_profiles');
            if (!File::exists($tempDir)) {
                File::makeDirectory($tempDir, 0755, true);
            }

            $file->move($tempDir, $filename);
            $tempPath = 'temp_profiles/' . $filename;
        }

        /**
         * SUBMODULE: VERIFICATION CODE GENERATION + EMAIL
         * -----------------------------------------
         * Connected to: Mail (VerificationCodeMail)
         */
        $verificationCode = rand(100000, 999999);

        Mail::to($request->email)
            ->send(new VerificationCodeMail($request->name, $verificationCode));

        /**
         * SUBMODULE: SESSION STORAGE
         * -----------------------------------------
         * Connected to: verifyCode()
         */
        session([
            'registration_data' => [
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password,
                'profile_image' => $tempPath,
            ],
            'verification_code' => $verificationCode,
        ]);

        return redirect()->route('acctv.form');
    }


    /**
     * =========================================================
     * MODULE 4: ACCOUNT VERIFICATION
     * =========================================================
     * Connected from: signup()
     */

    public function showVerificationPage()
    {
        if (!session()->has('registration_data')) {
            return redirect()->route('signup.form');
        }

        return view('auth.acctv');
    }

    public function verifyCode(Request $request)
    {
        $request->validate(['code' => 'required']);

        /**
         * SUBMODULE: CODE VALIDATION
         */
        if ($request->code != session('verification_code')) {
            return back()->with('error', 'Invalid verification code.');
        }

        $data = session('registration_data');

        /**
         * SUBMODULE: MOVE FILE TO FINAL STORAGE
         */
        $path = null;
        if ($data['profile_image'] && File::exists(public_path($data['profile_image']))) {
            $filename = basename($data['profile_image']);
            $finalDir = public_path('profiles');

            if (!File::exists($finalDir)) {
                File::makeDirectory($finalDir, 0755, true);
            }

            File::move(public_path($data['profile_image']), $finalDir . '/' . $filename);
            $path = 'profiles/' . $filename;
        }

        /**
         * SUBMODULE: USER CREATION
         * -----------------------------------------
         * Connected to: login()
         */
        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'researcher',
            'profile_image' => $path,
            'is_first_login' => true,
        ]);

        /**
         * SUBMODULE: SESSION CLEANUP
         */
        session()->forget(['registration_data', 'verification_code']);

        return redirect()->route('login.form')
            ->with('success', 'Account verified and created successfully.');
    }


    /**
     * =========================================================
     * MODULE 5: PROFILE MANAGEMENT
     * =========================================================
     */

    public function updateProfileImage(Request $request)
    {
        $request->validate([
            'profile_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = Auth::user();

        // Delete old image
        if ($user->profile_image && File::exists(public_path($user->profile_image))) {
            File::delete(public_path($user->profile_image));
        }

        // Save new image
        $file = $request->file('profile_image');
        $filename = time() . '_' . $file->getClientOriginalName();
        $file->move(public_path('profiles'), $filename);

        $user->profile_image = 'profiles/' . $filename;
        $user->save();

        return redirect()->back()->with('success', 'Profile picture updated successfully.');
    }


    /**
     * =========================================================
     * MODULE 6: PASSWORD RESET SYSTEM
     * =========================================================
     * Flow:
     * sendResetCode → verifyResetCode → resetPassword
     */

    public function showForgotPasswordForm()
    {
        return view('auth.forget');
    }

    public function sendResetCode(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        /**
         * SUBMODULE: USER CHECK
         */
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors([
                'email' => 'This email is not registered. Please sign up.'
            ]);
        }

        /**
         * SUBMODULE: GENERATE + STORE CODE
         */
        $resetCode = (string)rand(100000, 999999);

        session([
            'password_reset_email' => $request->email,
            'password_reset_code' => $resetCode,
        ]);

        /**
         * SUBMODULE: EMAIL SENDING
         */
        Mail::to($request->email)->send(new VerifCode($resetCode, $user->name));

        return redirect()->route('password.verifyForm')
            ->with('success', 'A reset code has been sent.');
    }

    public function showVerifyForm()
    {
        if (!session()->has('password_reset_email')) {
            return redirect()->route('password.forget');
        }

        return view('auth.reset_verify');
    }

    public function verifyResetCode(Request $request)
    {
        $request->validate(['code' => 'required|digits:6']);

        if ($request->code == session('password_reset_code')) {
            session(['password_reset_verified' => true]);
            return redirect()->route('password.resetForm');
        }

        return back()->withErrors(['code' => 'Incorrect code.']);
    }

    public function showResetPasswordForm()
    {
        if (!session('password_reset_verified')) {
            return redirect()->route('password.forget');
        }

        return view('auth.reset_password');
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::where('email', session('password_reset_email'))->first();

        if ($user) {
            $user->password = Hash::make($request->password);
            $user->is_first_login = false;
            $user->save();

            session()->forget([
                'password_reset_email',
                'password_reset_code',
                'password_reset_verified'
            ]);

            return redirect()->route('login.form')->with('success', 'Password updated.');
        }

        return redirect()->route('password.forget')->withErrors(['email' => 'Error occurred.']);
    }


    /**
     * =========================================================
     * MODULE 7: LOGOUT SYSTEM
     * =========================================================
     */

    public function logout(Request $request)
    {
        $user = Auth::user();

        if ($user) {
            /**
             * SUBMODULE: UPDATE LOGIN LOG
             */
            $lastLogin = LoginLog::where('user_id', $user->id)
                ->latest('logged_in_at')
                ->first();

            if ($lastLogin) {
                $lastLogin->update([
                    'logged_out_at' => now(),
                ]);
            }

            /**
             * SUBMODULE: FIRST LOGIN FLAG
             */
            if ($user->role === 'researcher') {
                $user->update(['is_first_login' => false]);
            }
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }


    /**
     * =========================================================
     * MODULE 8: PASSWORD UPDATE (LOGGED-IN USER)
     * =========================================================
     */

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
            'is_first_login' => false,
        ]);

        return back()->with('success', 'Password updated successfully.');
    }

    //shows the settings which is the same for all users
    public function showSettings()
    {
        $user = auth()->user();
        return view('auth.settings', compact('user'));
    }
}
