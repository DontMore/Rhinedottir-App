<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EmailSetting;
use App\Models\ApiSetting;
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

    public function apiSettings()
    {
        $settings = ApiSetting::first();
        return view('settings.api', compact('settings'));
    }

    public function updateApiSettings(Request $request)
    {
        $request->validate([
            'gas_web_app_url' => 'nullable|url',
            'is_active' => 'boolean',
            'push_is_active' => 'boolean',
            'push_interval' => 'required|in:everyMinute,everyFiveMinutes,everyTenMinutes,everyThirtyMinutes,hourly,daily',
        ]);

        $settings = ApiSetting::first();
        $isActive = $request->has('is_active') ? $request->is_active : false;
        $pushIsActive = $request->has('push_is_active') ? $request->push_is_active : false;

        $data = [
            'gas_web_app_url' => $request->gas_web_app_url,
            'is_active' => $isActive,
            'push_is_active' => $pushIsActive,
            'push_interval' => $request->push_interval,
        ];

        if (!$settings) {
            ApiSetting::create($data);
        } else {
            $settings->update($data);
        }

        Alert::success('Success', 'API settings updated successfully');
        return back();
    }

    public function regenerateApiToken()
    {
        $settings = ApiSetting::first();
        
        if (!$settings) {
            $settings = ApiSetting::create([
                'is_active' => false,
            ]);
        } else {
            $settings->update([
                'api_token' => \Illuminate\Support\Str::random(60)
            ]);
        }

        Alert::success('Success', 'API Token regenerated successfully');
        return back();
    }
}