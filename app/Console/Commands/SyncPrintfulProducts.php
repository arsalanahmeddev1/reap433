<?php

namespace App\Console\Commands;

use App\Services\PrintfulProductSyncService;
use Illuminate\Console\Command;

class SyncPrintfulProducts extends Command
{
    protected $signature = 'printful:sync-products';

    protected $description = 'Sync store products and variants from Printful';

    public function handle(PrintfulProductSyncService $syncService): int
    {
        $this->info('Fetching products...');

        $result = $syncService->sync();

        if (! $result['success']) {
            $this->error($result['message']);

            return self::FAILURE;
        }

        if ($result['synced_products'] === 0 && $result['synced_variants'] === 0) {
            $this->warn($result['message']);

            return self::SUCCESS;
        }

        $this->info($result['message']);

        if ($result['failed_products'] > 0) {
            $this->warn(sprintf('%d product(s) failed.', $result['failed_products']));
        }

        return self::SUCCESS;
    }
}
