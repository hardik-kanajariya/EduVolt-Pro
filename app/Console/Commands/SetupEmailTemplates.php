<?php

namespace App\Console\Commands;

use App\Models\EmailTemplate;
use Illuminate\Console\Command;

class SetupEmailTemplates extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'email:setup-templates';

    /**
     * The console command description.
     */
    protected $description = 'Set up default email templates for the application';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Setting up default email templates...');

        $seeder = new \Database\Seeders\EmailTemplateSeeder();
        $seeder->run();

        $count = EmailTemplate::count();
        $this->info("Successfully set up {$count} email templates.");

        return Command::SUCCESS;
    }
}
