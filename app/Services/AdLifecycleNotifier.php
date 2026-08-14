<?php

namespace App\Services;

use App\Mail\AdActivated;
use App\Mail\AdExpiringSoon;
use App\Models\Ad;
use Illuminate\Support\Facades\Mail;

class AdLifecycleNotifier
{
    public function notifyActivationIfDue(Ad $ad): bool
    {
        if (! $ad->is_active || ! $ad->advertiser_email || $ad->activation_notified_at || ($ad->starts_at && $ad->starts_at->isFuture()) || ($ad->expires_at && $ad->expires_at->isPast())) {
            return false;
        }
        Mail::to($ad->advertiser_email)->send(new AdActivated($ad));
        $ad->forceFill(['activation_notified_at' => now()])->save();

        return true;
    }

    public function notifyExpiryIfDue(Ad $ad): bool
    {
        if (! $ad->is_active || ! $ad->advertiser_email || $ad->expiry_warning_notified_at || ! $ad->expires_at || $ad->expires_at->isPast() || $ad->expires_at->greaterThan(now()->addWeek())) {
            return false;
        }
        Mail::to($ad->advertiser_email)->send(new AdExpiringSoon($ad));
        $ad->forceFill(['expiry_warning_notified_at' => now()])->save();

        return true;
    }
}
