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
        return view('pages.contact', [
            'content' => SiteSetting::valueFor('contact_us', ''),
            'email' => SiteSetting::valueFor('contact_email', ''),
            'whatsapp' => SiteSetting::valueFor('whatsapp_number', ''),
            'socialLinks' => SiteSetting::json('social_links'),
        ]);
    }
}
