<?php

namespace App\Console\Commands;

use App\Services\EmailService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ProcessEmailQueue extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'email:process
                          {--limit=50 : Maximum number of emails to process}
                          {--retry : Process only failed emails}';

    /**
     * The console command description.
     */
    protected $description = 'Process pending and scheduled emails';

    protected EmailService $emailService;

    public function __construct(EmailService $emailService)
    {
        parent::__construct();
        $this->emailService = $emailService;
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting email processing...');

        try {
            if ($this->option('retry')) {
                $this->info('Processing failed emails...');
                $this->emailService->retryFailedEmails();
            } else {
                $this->info('Processing pending emails...');
                $this->emailService->processPendingEmails();

                $this->info('Processing scheduled bulk emails...');
                $this->emailService->processScheduledBulkEmails();
            }

            $this->info('Email processing completed successfully.');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Email processing failed: ' . $e->getMessage());
            Log::error('Email processing command failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return Command::FAILURE;
        }
    }
}
