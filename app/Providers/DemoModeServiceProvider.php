<?php

namespace App\Providers;

use App\Services\DemoCredentialsService;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class DemoModeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        if (!DemoCredentialsService::isDemoMode()) {
            return;
        }

        // Register render hooks for all panels
        $this->registerLoginRenderHooks();
    }

    /**
     * Register render hooks for login forms
     */
    private function registerLoginRenderHooks(): void
    {
        // Hook after login form to inject demo credentials script
        FilamentView::registerRenderHook(
            PanelsRenderHook::AUTH_LOGIN_FORM_AFTER,
            fn(): string => $this->getDemoCredentialsScript(),
        );

        // Hook before login form to show demo notice
        FilamentView::registerRenderHook(
            PanelsRenderHook::AUTH_LOGIN_FORM_BEFORE,
            fn(): string => $this->getDemoNotice(),
        );
    }

    /**
     * Get demo credentials script based on current panel
     */
    private function getDemoCredentialsScript(): string
    {
        $panelId = filament()->getCurrentPanel()?->getId();

        if (!$panelId) {
            return '';
        }

        $credentials = DemoCredentialsService::getCredentials($panelId);

        if (empty($credentials)) {
            return '';
        }

        return "
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Auto-fill demo credentials
                const emailField = document.querySelector('input[name=\"data.email\"], input[type=\"email\"]');
                const passwordField = document.querySelector('input[name=\"data.password\"], input[type=\"password\"]');
                
                if (emailField && passwordField) {
                    emailField.value = '{$credentials['email']}';
                    passwordField.value = '{$credentials['password']}';
                    
                    // Trigger input events to notify Alpine.js/Livewire
                    emailField.dispatchEvent(new Event('input', { bubbles: true }));
                    passwordField.dispatchEvent(new Event('input', { bubbles: true }));
                }
            });
        </script>
        ";
    }

    /**
     * Get demo notice HTML
     */
    private function getDemoNotice(): string
    {
        $panelId = filament()->getCurrentPanel()?->getId();

        if (!$panelId) {
            return '';
        }

        $credentials = DemoCredentialsService::getCredentials($panelId);

        if (empty($credentials)) {
            return '';
        }

        return "
        <div class=\"fi-demo-notice mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg\">
            <div class=\"flex items-start space-x-3\">
                <div class=\"flex-shrink-0\">
                    <svg class=\"h-5 w-5 text-blue-600\" fill=\"currentColor\" viewBox=\"0 0 20 20\">
                        <path fill-rule=\"evenodd\" d=\"M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z\" clip-rule=\"evenodd\"></path>
                    </svg>
                </div>
                <div class=\"flex-1\">
                    <h3 class=\"text-sm font-semibold text-blue-800\">Demo Mode Active</h3>
                    <p class=\"text-sm text-blue-700 mt-1\">
                        Login credentials have been pre-filled for <strong>{$credentials['role']}</strong> access.
                        <br>
                        <span class=\"font-mono text-xs bg-blue-100 px-2 py-1 rounded mt-1 inline-block\">
                            {$credentials['email']} / {$credentials['password']}
                        </span>
                    </p>
                </div>
            </div>
        </div>
        ";
    }
}
