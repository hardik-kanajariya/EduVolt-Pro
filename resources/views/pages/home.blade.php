@extends('layouts.guest')

@section('title', 'EduVault Pro - Complete Digital Education Management Solution')
@section('description', 'Transform your school with EduVault Pro - comprehensive school management system with student management, attendance tracking, fee management, library system, and more. Built with Laravel & Filament.')
@section('keywords', 'school management system, education software, student management, teacher portal, attendance tracking, fee management, library system, exam management, Laravel, Filament')

@section('content')
<!-- Hero Section -->
<section class="relative bg-gradient-to-br from-primary-50 to-blue-50 overflow-hidden">
    <!-- Background Pattern -->
    <div class="absolute inset-0 opacity-10">
        <svg class="w-full h-full" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse">
                    <path d="M 10 0 L 0 0 0 10" fill="none" stroke="currentColor" stroke-width="0.5" />
                </pattern>
            </defs>
            <rect width="100" height="100" fill="url(#grid)" />
        </svg>
    </div>

    <div class="relative container-custom section-padding">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <!-- Content -->
            <div class="text-center lg:text-left">
                <h1 class="heading-1 mb-6 animate-fade-in">
                    Complete <span class="text-primary-600">Digital Education</span> Management Solution
                </h1>
                <p class="text-body mb-8 animate-slide-up">
                    Streamline your school operations with EduVault Pro - a comprehensive management system designed for
                    modern educational institutions. Manage students, teachers, attendance, fees, library, and more with
                    our advanced platform.
                </p>

                <!-- Feature Highlights -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8">
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0 w-6 h-6 bg-green-100 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                    clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <span class="text-gray-700 font-medium">Multi-Panel Interface</span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0 w-6 h-6 bg-green-100 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                    clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <span class="text-gray-700 font-medium">Real-time Analytics</span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0 w-6 h-6 bg-green-100 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                    clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <span class="text-gray-700 font-medium">Mobile-Friendly Design</span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0 w-6 h-6 bg-green-100 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                    clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <span class="text-gray-700 font-medium">One-time Purchase</span>
                    </div>
                </div>

                <!-- CTA Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                    <a href="{{ route('contact') }}" class="btn-primary text-lg px-8 py-3">
                        Get Started Today
                    </a>
                    <a href="{{ route('features') }}" class="btn-secondary text-lg px-8 py-3">
                        Explore Features
                    </a>
                </div>

                <!-- Trust Indicators -->
                <div class="mt-8 pt-8 border-t border-gray-200">
                    <p class="text-sm text-gray-500 mb-4">Trusted by 500+ Educational Institutions</p>
                    <div class="flex items-center justify-center lg:justify-start space-x-6 opacity-60">
                        <span class="text-gray-400 font-medium">Laravel 11</span>
                        <span class="text-gray-400 font-medium">Filament v3</span>
                        <span class="text-gray-400 font-medium">PHP 8.2+</span>
                        <span class="text-gray-400 font-medium">MySQL</span>
                    </div>
                </div>
            </div>

            <!-- Hero Image/Illustration -->
            <div class="relative">
                <div class="relative bg-white rounded-2xl shadow-2xl p-8 mx-auto max-w-md lg:max-w-none">
                    <!-- Dashboard Preview -->
                    <div class="space-y-4">
                        <!-- Header -->
                        <div class="flex items-center justify-between pb-4 border-b border-gray-200">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-primary-100 rounded-lg flex items-center justify-center">
                                    <div class="w-4 h-4 bg-primary-600 rounded"></div>
                                </div>
                                <span class="font-semibold text-gray-900">EduVault Pro</span>
                            </div>
                            <div class="flex space-x-2">
                                <div class="w-3 h-3 bg-red-400 rounded-full"></div>
                                <div class="w-3 h-3 bg-yellow-400 rounded-full"></div>
                                <div class="w-3 h-3 bg-green-400 rounded-full"></div>
                            </div>
                        </div>

                        <!-- Stats Cards -->
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-blue-50 p-4 rounded-lg">
                                <div class="text-2xl font-bold text-blue-600">1,247</div>
                                <div class="text-sm text-blue-600">Students</div>
                            </div>
                            <div class="bg-green-50 p-4 rounded-lg">
                                <div class="text-2xl font-bold text-green-600">89</div>
                                <div class="text-sm text-green-600">Teachers</div>
                            </div>
                        </div>

                        <!-- Chart Placeholder -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="flex items-end space-x-2 h-20">
                                <div class="bg-primary-300 w-4 h-12 rounded-t"></div>
                                <div class="bg-primary-400 w-4 h-16 rounded-t"></div>
                                <div class="bg-primary-500 w-4 h-10 rounded-t"></div>
                                <div class="bg-primary-600 w-4 h-20 rounded-t"></div>
                                <div class="bg-primary-400 w-4 h-8 rounded-t"></div>
                                <div class="bg-primary-300 w-4 h-14 rounded-t"></div>
                            </div>
                            <div class="text-sm text-gray-600 mt-2">Attendance Analytics</div>
                        </div>

                        <!-- Navigation -->
                        <div class="flex space-x-4 pt-2">
                            <div class="w-full bg-primary-100 h-2 rounded"></div>
                            <div class="w-full bg-gray-200 h-2 rounded"></div>
                            <div class="w-full bg-gray-200 h-2 rounded"></div>
                        </div>
                    </div>
                </div>

                <!-- Floating Elements -->
                <div
                    class="absolute -top-4 -right-4 w-20 h-20 bg-yellow-200 rounded-full opacity-60 animate-bounce-slow">
                </div>
                <div class="absolute -bottom-4 -left-4 w-16 h-16 bg-blue-200 rounded-full opacity-60 animate-bounce-slow"
                    style="animation-delay: 1s;"></div>
            </div>
        </div>
    </div>
</section>

<!-- Features Overview -->
<section class="section-padding bg-white">
    <div class="container-custom">
        <div class="text-center mb-16">
            <h2 class="heading-2 mb-6">
                Everything You Need to <span class="text-primary-600">Manage Your School</span>
            </h2>
            <p class="text-body max-w-3xl mx-auto">
                EduVault Pro provides a complete suite of tools to streamline every aspect of your educational
                institution, from student enrollment to graduation.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Feature Cards -->
            <div
                class="bg-white p-8 rounded-xl shadow-lg border border-gray-100 hover:shadow-xl transition-shadow duration-300">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-6">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m3 4.197a4 4 0 11-3-6.943 4 4 0 013 6.943z">
                        </path>
                    </svg>
                </div>
                <h3 class="heading-3 mb-4">Student Management</h3>
                <p class="text-gray-600 leading-relaxed">
                    Comprehensive student profiles, enrollment tracking, academic progress monitoring, and parent
                    communication tools.
                </p>
            </div>

            <div
                class="bg-white p-8 rounded-xl shadow-lg border border-gray-100 hover:shadow-xl transition-shadow duration-300">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mb-6">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4">
                        </path>
                    </svg>
                </div>
                <h3 class="heading-3 mb-4">Attendance Tracking</h3>
                <p class="text-gray-600 leading-relaxed">
                    Real-time attendance marking, automated reports, parent notifications, and detailed analytics for
                    better monitoring.
                </p>
            </div>

            <div
                class="bg-white p-8 rounded-xl shadow-lg border border-gray-100 hover:shadow-xl transition-shadow duration-300">
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center mb-6">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1">
                        </path>
                    </svg>
                </div>
                <h3 class="heading-3 mb-4">Fee Management</h3>
                <p class="text-gray-600 leading-relaxed">
                    Complete fee structure management, payment tracking, receipt generation, and financial reporting
                    system.
                </p>
            </div>

            <div
                class="bg-white p-8 rounded-xl shadow-lg border border-gray-100 hover:shadow-xl transition-shadow duration-300">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mb-6">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                        </path>
                    </svg>
                </div>
                <h3 class="heading-3 mb-4">Library System</h3>
                <p class="text-gray-600 leading-relaxed">
                    Digital library management with book cataloging, issue tracking, fine management, and reading
                    analytics.
                </p>
            </div>

            <div
                class="bg-white p-8 rounded-xl shadow-lg border border-gray-100 hover:shadow-xl transition-shadow duration-300">
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center mb-6">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                </div>
                <h3 class="heading-3 mb-4">Exam Management</h3>
                <p class="text-gray-600 leading-relaxed">
                    Comprehensive exam scheduling, marks entry, report card generation, and performance analytics.
                </p>
            </div>

            <div
                class="bg-white p-8 rounded-xl shadow-lg border border-gray-100 hover:shadow-xl transition-shadow duration-300">
                <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center mb-6">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                        </path>
                    </svg>
                </div>
                <h3 class="heading-3 mb-4">Reports & Analytics</h3>
                <p class="text-gray-600 leading-relaxed">
                    Powerful reporting tools with visual analytics, custom report generation, and data-driven insights.
                </p>
            </div>
        </div>

        <div class="text-center mt-12">
            <a href="{{ route('features') }}" class="btn-primary text-lg px-8 py-3">
                View All Features
            </a>
        </div>
    </div>
</section>

<!-- Panels Overview -->
<section class="section-padding bg-gray-50">
    <div class="container-custom">
        <div class="text-center mb-16">
            <h2 class="heading-2 mb-6">
                <span class="text-primary-600">Multi-Panel</span> Interface for Every User
            </h2>
            <p class="text-body max-w-3xl mx-auto">
                EduVault Pro provides specialized dashboards for different user roles, ensuring everyone has access to
                the tools they need.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <!-- Admin Panel -->
            <div class="bg-white p-8 rounded-xl shadow-lg text-center hover:shadow-xl transition-shadow duration-300">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.031 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                        </path>
                    </svg>
                </div>
                <h3 class="heading-3 mb-4">Admin Portal</h3>
                <p class="text-gray-600 mb-6">Complete system control with user management, system settings, and
                    comprehensive reports.</p>
                <a href="{{ route('filament.admin.auth.login') }}" class="btn-primary w-full">Access Admin</a>
            </div>

            <!-- Faculty Panel -->
            <div class="bg-white p-8 rounded-xl shadow-lg text-center hover:shadow-xl transition-shadow duration-300">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                        </path>
                    </svg>
                </div>
                <h3 class="heading-3 mb-4">Faculty Portal</h3>
                <p class="text-gray-600 mb-6">Teacher-focused interface for class management, attendance, and assignment
                    handling.</p>
                <a href="{{ route('filament.faculty.auth.login') }}" class="btn-primary w-full">Access Faculty</a>
            </div>

            <!-- Student Panel -->
            <div class="bg-white p-8 rounded-xl shadow-lg text-center hover:shadow-xl transition-shadow duration-300">
                <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 14l9-5-9-5-9 5 9 5z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z">
                        </path>
                    </svg>
                </div>
                <h3 class="heading-3 mb-4">Student Portal</h3>
                <p class="text-gray-600 mb-6">Student dashboard with grades, assignments, attendance, and academic
                    progress.</p>
                <a href="{{ route('filament.student.auth.login') }}" class="btn-primary w-full">Access Student</a>
            </div>

            <!-- Parent Panel -->
            <div class="bg-white p-8 rounded-xl shadow-lg text-center hover:shadow-xl transition-shadow duration-300">
                <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z">
                        </path>
                    </svg>
                </div>
                <h3 class="heading-3 mb-4">Parent Portal</h3>
                <p class="text-gray-600 mb-6">Parent access to monitor child's progress, attendance, fees, and school
                    communication.</p>
                <a href="{{ route('filament.parent.auth.login') }}" class="btn-primary w-full">Access Parent</a>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="section-padding bg-primary-600 text-white">
    <div class="container-custom text-center">
        <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold font-heading mb-6">
            Ready to Transform Your School?
        </h2>
        <p class="text-xl text-primary-100 mb-8 max-w-3xl mx-auto">
            Join hundreds of educational institutions already using EduVault Pro to streamline their operations and
            improve educational outcomes.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('contact') }}"
                class="bg-white text-primary-600 hover:bg-gray-100 font-medium py-3 px-8 rounded-lg transition-colors duration-200 text-lg">
                Start Free Trial
            </a>
            <a href="{{ route('pricing') }}"
                class="border-2 border-white text-white hover:bg-white hover:text-primary-600 font-medium py-3 px-8 rounded-lg transition-colors duration-200 text-lg">
                View Pricing
            </a>
        </div>

        <div class="mt-12 grid grid-cols-1 md:grid-cols-3 gap-8 max-w-4xl mx-auto">
            <div class="text-center">
                <div class="text-3xl font-bold mb-2">500+</div>
                <div class="text-primary-200">Schools Using</div>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold mb-2">50K+</div>
                <div class="text-primary-200">Students Managed</div>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold mb-2">99.9%</div>
                <div class="text-primary-200">Uptime Guarantee</div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@endpush