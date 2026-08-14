<?php

namespace App\Console\Commands;

use App\Models\Ad;
use App\Services\AdLifecycleNotifier;
use Illuminate\Console\Command;

class SendAdLifecycleNotifications extends Command
{
    protected $signature = 'ads:send-lifecycle-notifications';

    protected $description = 'Email advertisers when campaigns start and one week before they expire';

    public function handle(AdLifecycleNotifier $notifier): int
    {
        Ad::query()->where('is_active', true)->whereNotNull('advertiser_email')->chunkById(100, function ($ads) use ($notifier) {
            foreach ($ads as $ad) {
                $notifier->notifyActivationIfDue($ad);
                $notifier->notifyExpiryIfDue($ad);
            }
        });

        return self::SUCCESS;
    }
}
