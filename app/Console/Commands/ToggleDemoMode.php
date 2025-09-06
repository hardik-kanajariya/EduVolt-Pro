<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ToggleDemoMode extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'demo:toggle {--enable : Enable demo mode} {--disable : Disable demo mode}';

    /**
     * The console command description.
     */
    protected $description = 'Toggle demo mode on/off for auto-filling login credentials';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $envPath = base_path('.env');

        if (!file_exists($envPath)) {
            $this->error('.env file not found!');
            return 1;
        }

        $envContent = file_get_contents($envPath);

        if ($this->option('enable')) {
            $newContent = $this->updateEnvVariable($envContent, 'DEMO_MODE', 'true');
            $this->info('Demo mode has been ENABLED.');
            $this->info('Login forms will now auto-fill with demo credentials.');
        } elseif ($this->option('disable')) {
            $newContent = $this->updateEnvVariable($envContent, 'DEMO_MODE', 'false');
            $this->info('Demo mode has been DISABLED.');
            $this->info('Login forms will no longer auto-fill credentials.');
        } else {
            // Toggle current state
            $currentValue = env('DEMO_MODE', false);
            $newValue = $currentValue ? 'false' : 'true';
            $newContent = $this->updateEnvVariable($envContent, 'DEMO_MODE', $newValue);

            if ($newValue === 'true') {
                $this->info('Demo mode has been ENABLED.');
                $this->info('Login forms will now auto-fill with demo credentials.');
            } else {
                $this->info('Demo mode has been DISABLED.');
                $this->info('Login forms will no longer auto-fill credentials.');
            }
        }

        file_put_contents($envPath, $newContent);

        // Display current demo credentials if enabled
        if (env('DEMO_MODE', false) || (!$this->option('disable') && $newContent && str_contains($newContent, 'DEMO_MODE=true'))) {
            $this->displayDemoCredentials();
        }

        return 0;
    }

    /**
     * Update environment variable in .env content
     */
    private function updateEnvVariable(string $envContent, string $key, string $value): string
    {
        $pattern = "/^{$key}=.*$/m";
        $replacement = "{$key}={$value}";

        if (preg_match($pattern, $envContent)) {
            return preg_replace($pattern, $replacement, $envContent);
        } else {
            return $envContent . "\n{$replacement}";
        }
    }

    /**
     * Display demo credentials
     */
    private function displayDemoCredentials(): void
    {
        $this->newLine();
        $this->info('=== DEMO CREDENTIALS ===');
        $this->newLine();

        $credentials = [
            'Admin Panel' => ['email' => 'admin@eduvaultpro.com', 'password' => 'admin123'],
            'Faculty Panel' => ['email' => 'teacher@eduvaultpro.com', 'password' => 'teacher123'],
            'Student Panel' => ['email' => 'student@eduvaultpro.com', 'password' => 'student123'],
            'Parent Panel' => ['email' => 'parent@eduvaultpro.com', 'password' => 'parent123'],
            'School Panel' => ['email' => 'schooladmin@eduvaultpro.com', 'password' => 'admin123'],
        ];

        foreach ($credentials as $panel => $creds) {
            $this->line("<info>{$panel}:</info>");
            $this->line("  Email: <comment>{$creds['email']}</comment>");
            $this->line("  Password: <comment>{$creds['password']}</comment>");
            $this->newLine();
        }

        $this->info('These credentials will be auto-filled in login forms when demo mode is active.');
    }
}
