<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MarketingEmailPreferenceController extends Controller
{
    public function show(User $user): View
    {
        return view('marketing.unsubscribe', compact('user'));
    }

    public function unsubscribe(Request $request, User $user): RedirectResponse
    {
        $user->update([
            'marketing_email_opt_in' => false,
            'marketing_email_unsubscribed_at' => $user->marketing_email_unsubscribed_at ?? now(),
        ]);

        return redirect()->route('marketing.unsubscribe.show', [
            'user' => $user,
            'signature' => $request->query('signature'),
        ])->with('unsubscribed', true);
    }
}
