<?php

namespace App\Services;

class DemoCredentialsService
{
    /**
     * Get demo credentials for different panels
     */
    public static function getCredentials(string $panel): array
    {
        if (!config('app.demo_mode', false)) {
            return [];
        }

        $credentials = [
            'admin' => [
                'email' => 'admin@eduvaultpro.com',
                'password' => 'admin123',
                'name' => 'Super Administrator',
                'role' => 'Super Admin'
            ],
            'faculty' => [
                'email' => 'teacher@eduvaultpro.com',
                'password' => 'teacher123',
                'name' => 'John Teacher',
                'role' => 'Teacher'
            ],
            'student' => [
                'email' => 'student@eduvaultpro.com',
                'password' => 'student123',
                'name' => 'Jane Student',
                'role' => 'Student'
            ],
            'parent' => [
                'email' => 'parent@eduvaultpro.com',
                'password' => 'parent123',
                'name' => 'Parent Smith',
                'role' => 'Parent'
            ],
            'school' => [
                'email' => 'schooladmin@eduvaultpro.com',
                'password' => 'admin123',
                'name' => 'School Administrator',
                'role' => 'School Admin'
            ]
        ];

        return $credentials[$panel] ?? [];
    }

    /**
     * Get all demo credentials for display
     */
    public static function getAllCredentials(): array
    {
        if (!config('app.demo_mode', false)) {
            return [];
        }

        return [
            'Admin Panel' => self::getCredentials('admin'),
            'Faculty Panel' => self::getCredentials('faculty'),
            'Student Panel' => self::getCredentials('student'),
            'Parent Panel' => self::getCredentials('parent'),
            'School Panel' => self::getCredentials('school'),
        ];
    }

    /**
     * Check if demo mode is enabled
     */
    public static function isDemoMode(): bool
    {
        return config('app.demo_mode', false);
    }

    /**
     * Generate JavaScript for auto-filling login forms
     */
    public static function getAutoFillScript(string $panel): string
    {
        if (!self::isDemoMode()) {
            return '';
        }

        $credentials = self::getCredentials($panel);
        if (empty($credentials)) {
            return '';
        }

        return "
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Auto-fill demo credentials
                const emailField = document.querySelector('input[name=\"email\"], input[type=\"email\"]');
                const passwordField = document.querySelector('input[name=\"password\"], input[type=\"password\"]');
                
                if (emailField && passwordField) {
                    emailField.value = '{$credentials['email']}';
                    passwordField.value = '{$credentials['password']}';
                    
                    // Add demo notice
                    const form = emailField.closest('form');
                    if (form && !form.querySelector('.demo-notice')) {
                        const demoNotice = document.createElement('div');
                        demoNotice.className = 'demo-notice bg-blue-50 border border-blue-200 text-blue-800 px-4 py-3 rounded-lg mb-4';
                        demoNotice.innerHTML = `
                            <div class=\"flex items-center\">
                                <svg class=\"w-5 h-5 mr-2\" fill=\"currentColor\" viewBox=\"0 0 20 20\">
                                    <path fill-rule=\"evenodd\" d=\"M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z\" clip-rule=\"evenodd\"></path>
                                </svg>
                                <div>
                                    <p class=\"font-semibold\">Demo Mode Active</p>
                                    <p class=\"text-sm\">Login credentials pre-filled for {$credentials['role']} ({$credentials['name']})</p>
                                </div>
                            </div>
                        `;
                        form.parentNode.insertBefore(demoNotice, form);
                    }
                }
            });
        </script>
        ";
    }
}
