<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;

class SiteSettingController extends Controller
{
    public function edit()
    {
        return view('admin.site_settings.edit', [
            'settings' => [
                'privacy_policy' => SiteSetting::valueFor('privacy_policy', ''),
                'about_us' => SiteSetting::valueFor('about_us', ''),
                'contact_us' => SiteSetting::valueFor('contact_us', ''),
                'contact_email' => SiteSetting::valueFor('contact_email', ''),
                'whatsapp_number' => SiteSetting::valueFor('whatsapp_number', ''),
                'social_links' => SiteSetting::json('social_links'),
            ],
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'privacy_policy' => ['required', 'string', 'max:50000'],
            'about_us' => ['required', 'string', 'max:50000'],
            'contact_us' => ['required', 'string', 'max:50000'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'whatsapp_number' => ['nullable', 'string', 'max:30', 'regex:/^[0-9+()\-\s]+$/'],
            'social_links' => ['nullable', 'array'],
            'social_links.*' => ['nullable', 'url', 'max:2048'],
        ]);

        foreach (['privacy_policy', 'about_us', 'contact_us', 'contact_email', 'whatsapp_number'] as $key) {
            SiteSetting::put($key, $validated[$key] ?? '');
        }
        SiteSetting::put('social_links', array_filter($validated['social_links'] ?? []));

        return back()->with('success', 'Website content and contact settings updated.');
    }
}
