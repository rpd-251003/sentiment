<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = [
            'app_name' => AppSetting::get('app_name', 'SENTIMENT'),
            'app_logo' => AppSetting::get('app_logo', 'SENTIMENT'),
            'maintenance_mode' => AppSetting::get('maintenance_mode', '0'),
            'hf_api_url' => AppSetting::get('hf_api_url', 'https://router.huggingface.co/hf-inference/models/w11wo/indonesian-roberta-base-sentiment-classifier'),
            'hf_token' => AppSetting::get('hf_token', ''),
        ];

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'app_name' => ['required', 'string', 'max:255'],
            'app_logo' => ['required', 'string', 'max:255'],
            'maintenance_mode' => ['nullable', 'in:0,1'],
            'hf_api_url' => ['required', 'url', 'max:500'],
            'hf_token' => ['required', 'string', 'max:255'],
        ]);

        // Handle checkbox: if not present, set to '0'
        $validated['maintenance_mode'] = $request->has('maintenance_mode') ? '1' : '0';

        foreach ($validated as $key => $value) {
            AppSetting::set($key, $value);
        }

        return redirect()->route('admin.settings.index')
            ->with('success', 'Pengaturan berhasil disimpan.');
    }
}
