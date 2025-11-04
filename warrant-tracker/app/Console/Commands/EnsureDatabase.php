<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

class EnsureDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:ensure {--seed : Run the AdminUserSeeder after migrations}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ensure DB connection is available and run migrations if tables are missing';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Checking database connection...');

        try {
            DB::connection()->getPdo();
        } catch (\Exception $e) {
            $this->error('Database connection failed: '.$e->getMessage());
            return 1;
        }

        $this->info('Database connection OK.');

        // If migrations table is missing or there are pending migrations, run migrate
        if (!Schema::hasTable('migrations')) {
            $this->info('Migrations table not found — running migrations...');
            Artisan::call('migrate', ['--force' => true]);
            $this->info(Artisan::output());
        } else {
            $this->info('Migrations table exists. Running migrations to apply any pending migrations...');
            Artisan::call('migrate', ['--force' => true]);
            $this->info(Artisan::output());
        }

        if ($this->option('seed')) {
            $this->info('Seeding AdminUserSeeder...');
            Artisan::call('db:seed', ['--class' => 'AdminUserSeeder', '--force' => true]);
            $this->info(Artisan::output());
        }

        $this->info('Database ensure complete.');
        return 0;
    }
}
