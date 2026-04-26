<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EmailSetting;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Artisan;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = EmailSetting::first();
        return view('settings.index', compact('settings'));
    }

    public function updateEmailSettings(Request $request)
    {
        $request->validate([
            'mail_host' => 'required|string',
            'mail_port' => 'required|integer|in:25,465,587,2525',
            'mail_encryption' => 'nullable|in:ssl,tls',
            'mail_username' => 'required|email',
            'mail_password' => 'required',
            'mail_from_address' => 'required|email',
        ]);

        $settings = EmailSetting::first();
        
        if (!$settings) {
            $settings = EmailSetting::create([
                'mail_host' => $request->mail_host,
                'mail_port' => $request->mail_port,
                'mail_encryption' => $request->mail_encryption,
                'mail_username' => $request->mail_username,
                'mail_password' => $request->mail_password,
                'mail_from_address' => $request->mail_from_address,
            ]);
        } else {
            $settings->update([
                'mail_host' => $request->mail_host,
                'mail_port' => $request->mail_port,
                'mail_encryption' => $request->mail_encryption,
                'mail_username' => $request->mail_username,
                'mail_password' => $request->mail_password,
                'mail_from_address' => $request->mail_from_address,
            ]);
        }

        // Refresh config runtime
        EmailSetting::updateConfig();
        
        // Clear cache
        Artisan::call('config:clear');

        Alert::success('Success', 'Email settings updated successfully');
        return back();
    }
}