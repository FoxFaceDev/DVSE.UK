<?php

namespace App\Services;

use App\Mail\AdActivated;
use App\Mail\AdExpiringSoon;
use App\Models\Ad;
use Illuminate\Support\Facades\Mail;
use Throwable;

class AdLifecycleNotifier
{
    private ?Throwable $lastFailure = null;

    public function notifyActivationIfDue(Ad $ad): bool
    {
        $this->lastFailure = null;

        if (! $ad->is_active || ! $ad->advertiser_email || $ad->activation_notified_at || ($ad->starts_at && $ad->starts_at->isFuture()) || ($ad->expires_at && $ad->expires_at->isPast())) {
            return false;
        }

        try {
            Mail::to($ad->advertiser_email)->send(new AdActivated($ad));
        } catch (Throwable $exception) {
            $this->lastFailure = $exception;
            report($exception);

            return false;
        }

        $ad->forceFill(['activation_notified_at' => now()])->save();

        return true;
    }

    public function notifyExpiryIfDue(Ad $ad): bool
    {
        $this->lastFailure = null;

        if (! $ad->is_active || ! $ad->advertiser_email || $ad->expiry_warning_notified_at || ! $ad->expires_at || $ad->expires_at->isPast() || $ad->expires_at->greaterThan(now()->addWeek())) {
            return false;
        }

        try {
            Mail::to($ad->advertiser_email)->send(new AdExpiringSoon($ad));
        } catch (Throwable $exception) {
            $this->lastFailure = $exception;
            report($exception);

            return false;
        }

        $ad->forceFill(['expiry_warning_notified_at' => now()])->save();

        return true;
    }

    public function lastFailure(): ?Throwable
    {
        return $this->lastFailure;
    }
}
