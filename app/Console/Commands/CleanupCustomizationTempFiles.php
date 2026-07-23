<?php

namespace App\Console\Commands;

use App\Models\ProductCustomizationFile;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanupCustomizationTempFiles extends Command
{
    protected $signature = 'customizations:cleanup-temp {--hours=48 : Delete temp files older than this many hours}';

    protected $description = 'Delete abandoned temporary product customization files';

    public function handle(): int
    {
        $hours = max(1, (int) $this->option('hours'));
        $cutoff = now()->subHours($hours);

        $files = ProductCustomizationFile::query()
            ->where('type', 'temp')
            ->where('created_at', '<', $cutoff)
            ->get();

        $deleted = 0;
        foreach ($files as $file) {
            Storage::disk($file->disk ?: 'public')->delete($file->path);
            $file->delete();
            $deleted++;
        }

        $this->info("Deleted {$deleted} temporary customization file(s).");

        return self::SUCCESS;
    }
}
