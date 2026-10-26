<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use App\Rules\CardColor;
use App\Support\HazardPage;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class HazardPageController extends Controller
{
    public function edit()
    {
        return view('admin.hazard_page.edit', [
            'settings' => HazardPage::settings(),
            'destinations' => HazardPage::destinations(),
        ]);
    }

    public function update(Request $request)
    {
        $destinationRule = Rule::in(array_keys(HazardPage::destinations()));
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:80'],
            'banner_active' => ['required', 'boolean'],
            'banner_destination' => ['required', $destinationRule],
            'banner' => ['required', 'array:en,ku', 'required_array_keys:en,ku'],
            'banner.*' => ['required', 'array:before,highlight,after,button'],
            'banner.*.before' => ['nullable', 'string', 'max:80'],
            'banner.*.highlight' => ['required', 'string', 'max:80'],
            'banner.*.after' => ['nullable', 'string', 'max:80'],
            'banner.*.button' => ['required', 'string', 'max:60'],
            'cards' => ['required', 'array:guarantee,help,clips,mock,progress,advice', 'size:6'],
            'cards.*' => ['required', 'array:label,icon,badge,destination,position,active,color'],
            'cards.*.label' => ['required', 'string', 'max:80'],
            'cards.*.icon' => ['required', Rule::in(HazardPage::ICONS)],
            'cards.*.badge' => ['nullable', 'string', 'max:16'],
            'cards.*.destination' => ['required', $destinationRule],
            'cards.*.position' => ['required', 'integer', 'min:1', 'max:99'],
            'cards.*.active' => ['required', 'boolean'],
            'cards.*.color' => ['required', new CardColor],
        ]);

        foreach ($validated['cards'] as &$card) {
            $card['color'] = CardColor::normalize($card['color']);
        }
        unset($card);

        SiteSetting::put('hazard_page', $validated);

        return back()->with('success', 'Hazard Perception page updated.');
    }
}
