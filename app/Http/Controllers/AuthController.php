<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;

class AuthController extends Controller
{
    // ... (existing methods)
    
    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::broker()->sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with('success', 'Link reset kata sandi telah dikirim ke email Anda.')
            : back()->withErrors(['email' => __($status)]);
    }

    public function showResetPassword(Request $request, $token)
    {
        return view('auth.reset-password', ['token' => $token, 'email' => $request->email]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::broker()->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('success', 'Kata sandi berhasil diubah. Silakan login kembali.')
            : back()->withErrors(['email' => __($status)]);
    }

    public function showRegister()
    {
        $departments = Department::with('faculty')->get();
        return view('auth.register', compact('departments'));
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'nim' => ['required', 'string', 'unique:users'],
            'role' => ['required', 'in:mahasiswa,dosen,guest'],
            'department_id' => [in_array($request->role, ['guest', 'dosen']) ? 'nullable' : 'required', 'exists:departments,id'],
            'affiliation' => ['nullable', 'string', 'max:255'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'nim' => $request->nim,
            'role' => $request->role,
            'is_verified' => false,
            'department_id' => in_array($request->role, ['guest', 'dosen']) ? null : $request->department_id,
            'affiliation' => $request->affiliation,
        ]);

        // Kirim email verifikasi standar Laravel
        event(new \Illuminate\Auth\Events\Registered($user));

        $message = 'Pendaftaran berhasil. Silakan cek email Anda untuk melakukan verifikasi akun sebelum login.';

        return redirect('/login')->with('success', $message);
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'nim' => ['required', 'string', 'unique:users,nim,' . $user->id],
            'affiliation' => ['nullable', 'string', 'max:255'],
        ]);

        $user->update([
            'name' => $request->name,
            'nim' => $request->nim,
            'affiliation' => $request->affiliation,
        ]);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        Auth::user()->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Kata sandi berhasil diubah.');
    }

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            
            // Sync is_verified jika email sudah diverifikasi melalui tautan Laravel
            if ($user->hasVerifiedEmail() && !$user->is_verified) {
                $user->update(['is_verified' => true]);
            }

            // Cek apakah email sudah diverifikasi (Kecualikan admin, superadmin, & user yg sudah di-verify admin)
            if (!$user->isAdmin() && !$user->is_verified && $user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail()) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Silakan verifikasi alamat email Anda terlebih dahulu sebelum login. Link verifikasi telah dikirimkan ke kotak masuk Anda.',
                ])->onlyInput('email');
            }

            $request->session()->regenerate();
            
            if ($user->isAdmin()) {
                return redirect()->intended(route('admin.dashboard'));
            }
            
            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->to(route('home'))->with('success', 'Anda telah berhasil keluar.');
    }
}
