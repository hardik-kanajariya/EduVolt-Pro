<?php

namespace App\Console\Commands;

use App\Services\DemoCredentialsService;
use Illuminate\Console\Command;

class ShowDemoInfo extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'demo:info';

    /**
     * The console command description.
     */
    protected $description = 'Show current demo mode status and available credentials';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDemoMode = DemoCredentialsService::isDemoMode();

        $this->info('=== DEMO MODE STATUS ===');
        $this->newLine();

        if ($isDemoMode) {
            $this->info('Demo Mode: <fg=green>ENABLED</fg=green>');
            $this->info('Login forms will auto-fill with demo credentials.');
            $this->newLine();

            $this->info('=== AVAILABLE DEMO CREDENTIALS ===');
            $this->newLine();

            $allCredentials = DemoCredentialsService::getAllCredentials();

            foreach ($allCredentials as $panel => $credentials) {
                $this->line("<info>{$panel}:</info>");
                $this->line("  Email: <comment>{$credentials['email']}</comment>");
                $this->line("  Password: <comment>{$credentials['password']}</comment>");
                $this->line("  Role: {$credentials['role']} ({$credentials['name']})");
                $this->newLine();
            }

            $this->info('=== PANEL URLS ===');
            $this->newLine();
            $this->line('Admin Panel: <comment>' . url('/admin') . '</comment>');
            $this->line('Faculty Panel: <comment>' . url('/faculty') . '</comment>');
            $this->line('Student Panel: <comment>' . url('/student') . '</comment>');
            $this->line('Parent Panel: <comment>' . url('/parent') . '</comment>');
            $this->line('School Panel: <comment>' . url('/school') . '</comment>');
        } else {
            $this->info('Demo Mode: <fg=red>DISABLED</fg=red>');
            $this->info('Login forms will not auto-fill credentials.');
            $this->newLine();
            $this->info('To enable demo mode, run: <comment>php artisan demo:toggle --enable</comment>');
        }

        $this->newLine();
        $this->info('=== DEMO MODE COMMANDS ===');
        $this->line('Enable:  <comment>php artisan demo:toggle --enable</comment>');
        $this->line('Disable: <comment>php artisan demo:toggle --disable</comment>');
        $this->line('Toggle:  <comment>php artisan demo:toggle</comment>');
        $this->line('Info:    <comment>php artisan demo:info</comment>');

        return 0;
    }
}
