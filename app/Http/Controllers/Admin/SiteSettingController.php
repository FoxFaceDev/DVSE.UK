<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;

class SiteSettingController extends Controller
{
    public function edit()
    {
        $whatsappPlacements = SiteSetting::valueFor('whatsapp_placements');

        return view('admin.site_settings.edit', [
            'settings' => [
                'privacy_policy' => SiteSetting::valueFor('privacy_policy', ''),
                'about_us' => SiteSetting::valueFor('about_us', ''),
                'contact_us' => SiteSetting::valueFor('contact_us', ''),
                'contact_email' => SiteSetting::valueFor('contact_email', ''),
                'whatsapp_number' => SiteSetting::valueFor('whatsapp_number', ''),
                'whatsapp_placements' => $whatsappPlacements === null
                    ? ['sections', 'subsections']
                    : (json_decode((string) $whatsappPlacements, true) ?: []),
                'social_links' => SiteSetting::socialLinks(),
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
            'whatsapp_placements' => ['nullable', 'array'],
            'whatsapp_placements.*' => ['string', 'in:home,sections,subsections,categories,practice,learning,mock_tests,account,auth,about,contact'],
            'social_links' => ['nullable', 'array'],
            'social_links.*' => ['array:url,active'],
            'social_links.*.url' => ['nullable', 'url', 'max:2048'],
            'social_links.*.active' => ['required', 'boolean'],
        ]);

        foreach (['privacy_policy', 'about_us', 'contact_us', 'contact_email', 'whatsapp_number'] as $key) {
            SiteSetting::put($key, $validated[$key] ?? '');
        }
        SiteSetting::put('social_links', collect($validated['social_links'] ?? [])->map(fn (array $link): array => [
            'url' => $link['url'] ?? '',
            'active' => (bool) $link['active'],
        ])->all());
        SiteSetting::put('whatsapp_placements', array_values($validated['whatsapp_placements'] ?? []));

        return back()->with('success', 'Website content and contact settings updated.');
    }
}
