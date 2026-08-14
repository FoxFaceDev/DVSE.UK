<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;

class PageController extends Controller
{
    public function about()
    {
        return view('pages.about', ['content' => SiteSetting::valueFor('about_us', '')]);
    }

    public function contact()
    {
        $socialLinks = collect(SiteSetting::socialLinks())
            ->filter(fn (array $link): bool => $link['active'] && filled($link['url']))
            ->map(fn (array $link): string => $link['url'])
            ->all();

        return view('pages.contact', [
            'content' => SiteSetting::valueFor('contact_us', ''),
            'email' => SiteSetting::valueFor('contact_email', ''),
            'whatsapp' => SiteSetting::valueFor('whatsapp_number', ''),
            'socialLinks' => $socialLinks,
        ]);
    }
}
