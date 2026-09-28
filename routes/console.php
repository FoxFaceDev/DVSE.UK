<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\Console\Command\Command;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('media:migrate-to-r2 {--dry-run : List files without copying them} {--force : Replace objects that already exist}', function () {
    $source = Storage::disk('public');
    $target = Storage::disk('r2');
    $files = $source->allFiles();

    if ($files === []) {
        $this->info('No files were found in storage/app/public.');

        return Command::SUCCESS;
    }

    $copied = 0;
    $skipped = 0;

    foreach ($files as $path) {
        if (! $this->option('force') && $target->exists($path)) {
            $this->line("SKIP {$path}");
            $skipped++;

            continue;
        }

        if ($this->option('dry-run')) {
            $this->line("COPY {$path}");
            $copied++;

            continue;
        }

        $stream = $source->readStream($path);

        if (! is_resource($stream)) {
            $this->error("Unable to read {$path}");

            return Command::FAILURE;
        }

        try {
            $target->writeStream($path, $stream, [
                'CacheControl' => 'public, max-age=31536000, immutable',
            ]);
        } finally {
            fclose($stream);
        }

        $this->line("COPIED {$path}");
        $copied++;
    }

    $verb = $this->option('dry-run') ? 'would be copied' : 'copied';
    $this->info("{$copied} file(s) {$verb}; {$skipped} skipped.");

    return Command::SUCCESS;
})->purpose('Copy existing public media to Cloudflare R2 without loading whole files into memory');

Artisan::command('media:check-r2', function () {
    $disk = Storage::disk('r2');
    $path = '_healthchecks/'.Str::uuid().'.txt';

    $disk->put($path, 'DVSE R2 connection check');

    if (! $disk->exists($path)) {
        $this->error('R2 accepted the request, but the test object could not be found.');

        return Command::FAILURE;
    }

    $disk->delete($path);
    $this->info('R2 read, write, and delete access is working.');

    return Command::SUCCESS;
})->purpose('Verify the configured Cloudflare R2 credentials and bucket access');

Schedule::command('ads:send-lifecycle-notifications')->hourly()->withoutOverlapping();
