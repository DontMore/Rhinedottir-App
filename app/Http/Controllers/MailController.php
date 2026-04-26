<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use App\Models\User;
use App\Models\EmailSetting;
use Illuminate\Support\Facades\Config;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use RealRashid\SweetAlert\Facades\Alert;

class MailController extends Controller
{
    private function configureMailSettings()
    {
        $settings = EmailSetting::first();
        if ($settings) {
            Config::set('mail.mailers.smtp.host', $settings->mail_host ?? 'smtp.gmail.com');
            Config::set('mail.mailers.smtp.port', $settings->mail_port ?? 587);
            Config::set('mail.mailers.smtp.encryption', $settings->mail_encryption ?? 'tls');
            Config::set('mail.mailers.smtp.username', $settings->mail_username);
            Config::set('mail.mailers.smtp.password', $settings->mail_password);
            Config::set('mail.from.address', $settings->mail_from_address);
        }
    }

    public function index(){
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $this->configureMailSettings();

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with(['status' => __($status)])
            : back()->withErrors(['email' => __($status)]);
    }

    public function resetPassword(string $token)
    {
        return view('auth.reset-password', ['token' => $token]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        // Konfigurasi mail settings sebelum reset password
        $this->configureMailSettings();

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));
                $user->save();
                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            Alert::success('Success', 'Password has been reset successfully');
            return redirect()->route('login');
        } else {
            return back()->withErrors(['email' => [__($status)]]);
        }
    }
}