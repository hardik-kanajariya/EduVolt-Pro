@extends('layouts.guest')

@section('title', 'Pricing Plans - EduVault Pro')
@section('description', 'Choose the perfect EduVault Pro plan for your educational institution. Transparent pricing with no hidden fees. Start your digital transformation today.')
@section('keywords', 'school management system pricing, education software cost, EduVault Pro plans, school software pricing')

@section('content')
    <!-- Hero Section -->
    <section class="bg-gradient-to-br from-primary-50 to-blue-50 section-padding">
        <div class="container-custom text-center">
            <h1 class="heading-1 mb-6">
                Simple, <span class="text-primary-600">Transparent</span> Pricing
            </h1>
            <p class="text-body max-w-3xl mx-auto mb-8">
                Choose the perfect plan for your educational institution. All plans include unlimited users, complete
                features, and dedicated support. No hidden fees, no surprises.
            </p>

            <!-- Billing Toggle -->
            <div class="flex items-center justify-center space-x-4 mb-8" x-data="{ annually: true }">
                <span class="text-gray-600" :class="{ 'text-primary-600 font-semibold': !annually }">Monthly</span>
                <button @click="annually = !annually"
                    class="relative inline-flex h-6 w-11 items-center rounded-full bg-gray-200 transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
                    :class="{ 'bg-primary-600': annually }">
                    <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"
                        :class="{ 'translate-x-6': annually, 'translate-x-1': !annually }"></span>
                </button>
                <span class="text-gray-600" :class="{ 'text-primary-600 font-semibold': annually }">
                    Annually
                    <span class="text-green-600 text-sm font-medium">(Save 20%)</span>
                </span>
            </div>
        </div>
    </section>

    <!-- Pricing Plans -->
    <section class="section-padding bg-white">
        <div class="container-custom" x-data="{ annually: true }">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-7xl mx-auto">

                <!-- Starter Plan -->
                <div
                    class="bg-white rounded-2xl border-2 border-gray-200 p-8 relative hover:border-primary-300 transition-colors duration-300">
                    <div class="text-center">
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Starter</h3>
                        <p class="text-gray-600 mb-6">Perfect for small schools and institutions</p>

                        <div class="mb-8">
                            <div x-show="annually">
                                <span class="text-4xl font-bold text-gray-900">$199</span>
                                <span class="text-gray-600">/month</span>
                                <div class="text-sm text-green-600 font-medium mt-1">
                                    Billed annually ($2,388/year)
                                </div>
                            </div>
                            <div x-show="!annually">
                                <span class="text-4xl font-bold text-gray-900">$249</span>
                                <span class="text-gray-600">/month</span>
                                <div class="text-sm text-gray-500 mt-1">
                                    Billed monthly
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4 mb-8">
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                            <span class="text-gray-700">Up to 500 students</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                            <span class="text-gray-700">Student Management Panel</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                            <span class="text-gray-700">Teacher Portal</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                            <span class="text-gray-700">Parent Dashboard</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                            <span class="text-gray-700">Basic Attendance Tracking</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                            <span class="text-gray-700">Grade Management</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                            <span class="text-gray-700">Email Support</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                            <span class="text-gray-700">5GB Storage</span>
                        </div>
                    </div>

                    <button class="w-full btn-secondary py-3 text-center">
                        Choose Starter
                    </button>
                </div>

                <!-- Professional Plan (Most Popular) -->
                <div
                    class="bg-white rounded-2xl border-2 border-primary-500 p-8 relative hover:border-primary-600 transition-colors duration-300 transform scale-105">
                    <!-- Popular Badge -->
                    <div class="absolute -top-4 left-1/2 transform -translate-x-1/2">
                        <span class="bg-primary-500 text-white px-6 py-2 rounded-full text-sm font-medium">
                            Most Popular
                        </span>
                    </div>

                    <div class="text-center">
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Professional</h3>
                        <p class="text-gray-600 mb-6">Ideal for medium to large schools</p>

                        <div class="mb-8">
                            <div x-show="annually">
                                <span class="text-4xl font-bold text-gray-900">$399</span>
                                <span class="text-gray-600">/month</span>
                                <div class="text-sm text-green-600 font-medium mt-1">
                                    Billed annually ($4,788/year)
                                </div>
                            </div>
                            <div x-show="!annually">
                                <span class="text-4xl font-bold text-gray-900">$499</span>
                                <span class="text-gray-600">/month</span>
                                <div class="text-sm text-gray-500 mt-1">
                                    Billed monthly
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4 mb-8">
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                            <span class="text-gray-700"><strong>Unlimited students</strong></span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                            <span class="text-gray-700">All Starter features</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                            <span class="text-gray-700">Administrative Panel</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                            <span class="text-gray-700">Advanced Attendance & Biometric</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                            <span class="text-gray-700">Financial Management</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                            <span class="text-gray-700">Exam Management</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                            <span class="text-gray-700">Library Management</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                            <span class="text-gray-700">Priority Support</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                            <span class="text-gray-700">50GB Storage</span>
                        </div>
                    </div>

                    <button class="w-full btn-primary py-3 text-center">
                        Choose Professional
                    </button>
                </div>

                <!-- Enterprise Plan -->
                <div
                    class="bg-white rounded-2xl border-2 border-gray-200 p-8 relative hover:border-primary-300 transition-colors duration-300">
                    <div class="text-center">
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Enterprise</h3>
                        <p class="text-gray-600 mb-6">For large institutions with complex needs</p>

                        <div class="mb-8">
                            <div x-show="annually">
                                <span class="text-4xl font-bold text-gray-900">$799</span>
                                <span class="text-gray-600">/month</span>
                                <div class="text-sm text-green-600 font-medium mt-1">
                                    Billed annually ($9,588/year)
                                </div>
                            </div>
                            <div x-show="!annually">
                                <span class="text-4xl font-bold text-gray-900">$999</span>
                                <span class="text-gray-600">/month</span>
                                <div class="text-sm text-gray-500 mt-1">
                                    Billed monthly
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4 mb-8">
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                            <span class="text-gray-700">All Professional features</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                            <span class="text-gray-700">Multi-campus Support</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                            <span class="text-gray-700">Custom Integrations</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                            <span class="text-gray-700">Advanced Analytics & Reporting</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                            <span class="text-gray-700">API Access</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                            <span class="text-gray-700">Dedicated Account Manager</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                            <span class="text-gray-700">24/7 Phone Support</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                            <span class="text-gray-700">Unlimited Storage</span>
                        </div>
                    </div>

                    <button class="w-full btn-secondary py-3 text-center">
                        Contact Sales
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- Feature Comparison -->
    <section class="section-padding bg-gray-50">
        <div class="container-custom">
            <div class="text-center mb-16">
                <h2 class="heading-2 mb-6">Detailed Feature Comparison</h2>
                <p class="text-body max-w-3xl mx-auto">
                    Compare all features across our pricing plans to find the perfect fit for your institution.
                </p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full bg-white rounded-lg shadow-sm border border-gray-200">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="text-left p-6 font-semibold text-gray-900">Features</th>
                            <th class="text-center p-6 font-semibold text-gray-900">Starter</th>
                            <th class="text-center p-6 font-semibold text-gray-900 bg-primary-50">Professional</th>
                            <th class="text-center p-6 font-semibold text-gray-900">Enterprise</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <tr>
                            <td class="p-6 font-medium text-gray-900">Student Limit</td>
                            <td class="p-6 text-center text-gray-600">500</td>
                            <td class="p-6 text-center text-gray-900 bg-primary-50">Unlimited</td>
                            <td class="p-6 text-center text-gray-900">Unlimited</td>
                        </tr>
                        <tr>
                            <td class="p-6 font-medium text-gray-900">User Panels</td>
                            <td class="p-6 text-center text-gray-600">3 (Student, Teacher, Parent)</td>
                            <td class="p-6 text-center text-gray-900 bg-primary-50">4 (+ Admin)</td>
                            <td class="p-6 text-center text-gray-900">4 (+ Custom)</td>
                        </tr>
                        <tr>
                            <td class="p-6 font-medium text-gray-900">Attendance Tracking</td>
                            <td class="p-6 text-center">
                                <svg class="w-5 h-5 text-green-500 mx-auto" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                            </td>
                            <td class="p-6 text-center bg-primary-50">
                                <svg class="w-5 h-5 text-green-500 mx-auto" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="text-xs text-gray-600 block mt-1">+ Biometric</span>
                            </td>
                            <td class="p-6 text-center">
                                <svg class="w-5 h-5 text-green-500 mx-auto" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="text-xs text-gray-600 block mt-1">+ Advanced</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="p-6 font-medium text-gray-900">Financial Management</td>
                            <td class="p-6 text-center">
                                <svg class="w-5 h-5 text-gray-400 mx-auto" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </td>
                            <td class="p-6 text-center bg-primary-50">
                                <svg class="w-5 h-5 text-green-500 mx-auto" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                            </td>
                            <td class="p-6 text-center">
                                <svg class="w-5 h-5 text-green-500 mx-auto" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                            </td>
                        </tr>
                        <tr>
                            <td class="p-6 font-medium text-gray-900">API Access</td>
                            <td class="p-6 text-center">
                                <svg class="w-5 h-5 text-gray-400 mx-auto" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </td>
                            <td class="p-6 text-center bg-primary-50">
                                <svg class="w-5 h-5 text-gray-400 mx-auto" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </td>
                            <td class="p-6 text-center">
                                <svg class="w-5 h-5 text-green-500 mx-auto" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                            </td>
                        </tr>
                        <tr>
                            <td class="p-6 font-medium text-gray-900">Storage</td>
                            <td class="p-6 text-center text-gray-600">5GB</td>
                            <td class="p-6 text-center text-gray-900 bg-primary-50">50GB</td>
                            <td class="p-6 text-center text-gray-900">Unlimited</td>
                        </tr>
                        <tr>
                            <td class="p-6 font-medium text-gray-900">Support Level</td>
                            <td class="p-6 text-center text-gray-600">Email</td>
                            <td class="p-6 text-center text-gray-900 bg-primary-50">Priority</td>
                            <td class="p-6 text-center text-gray-900">24/7 Dedicated</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <!-- Trust Section -->
    <section class="section-padding bg-white">
        <div class="container-custom">
            <div class="text-center mb-16">
                <h2 class="heading-2 mb-6">Trusted by Schools Worldwide</h2>
                <p class="text-body max-w-3xl mx-auto">
                    Join thousands of educational institutions that have transformed their operations with EduVault Pro.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="text-4xl font-bold text-primary-600 mb-2">500+</div>
                    <div class="text-gray-600">Schools Using EduVault Pro</div>
                </div>
                <div class="text-center">
                    <div class="text-4xl font-bold text-primary-600 mb-2">50,000+</div>
                    <div class="text-gray-600">Students Managed Daily</div>
                </div>
                <div class="text-center">
                    <div class="text-4xl font-bold text-primary-600 mb-2">99.9%</div>
                    <div class="text-gray-600">System Uptime</div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="section-padding bg-gray-50">
        <div class="container-custom">
            <div class="text-center mb-16">
                <h2 class="heading-2 mb-6">Pricing FAQs</h2>
                <p class="text-body max-w-3xl mx-auto">
                    Common questions about our pricing and billing.
                </p>
            </div>

            <div class="max-w-4xl mx-auto" x-data="{ openFaq: null }">
                <div class="space-y-4">
                    <!-- FAQ Item 1 -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <button @click="openFaq = openFaq === 1 ? null : 1"
                            class="w-full text-left px-6 py-4 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-inset">
                            <div class="flex justify-between items-center">
                                <h3 class="font-semibold text-gray-900">Can I change plans later?</h3>
                                <svg class="w-5 h-5 text-gray-500 transition-transform duration-200"
                                    :class="{ 'rotate-180': openFaq === 1 }" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </button>
                        <div x-show="openFaq === 1" x-transition class="px-6 pb-4">
                            <p class="text-gray-600">Yes, you can upgrade or downgrade your plan at any time. Changes will
                                be prorated and reflected in your next billing cycle.</p>
                        </div>
                    </div>

                    <!-- FAQ Item 2 -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <button @click="openFaq = openFaq === 2 ? null : 2"
                            class="w-full text-left px-6 py-4 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-inset">
                            <div class="flex justify-between items-center">
                                <h3 class="font-semibold text-gray-900">Is there a setup fee?</h3>
                                <svg class="w-5 h-5 text-gray-500 transition-transform duration-200"
                                    :class="{ 'rotate-180': openFaq === 2 }" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </button>
                        <div x-show="openFaq === 2" x-transition class="px-6 pb-4">
                            <p class="text-gray-600">No setup fees for any plan. We include complete setup, data migration,
                                and training as part of your subscription.</p>
                        </div>
                    </div>

                    <!-- FAQ Item 3 -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <button @click="openFaq = openFaq === 3 ? null : 3"
                            class="w-full text-left px-6 py-4 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-inset">
                            <div class="flex justify-between items-center">
                                <h3 class="font-semibold text-gray-900">What payment methods do you accept?</h3>
                                <svg class="w-5 h-5 text-gray-500 transition-transform duration-200"
                                    :class="{ 'rotate-180': openFaq === 3 }" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </button>
                        <div x-show="openFaq === 3" x-transition class="px-6 pb-4">
                            <p class="text-gray-600">We accept all major credit cards, bank transfers, and purchase orders
                                for annual subscriptions. Enterprise customers can also arrange invoicing.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Demo Credentials Section -->
    @include('components.demo-credentials')

    <!-- CTA Section -->
    <section class="section-padding bg-primary-600">
        <div class="container-custom text-center">
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-6">
                Ready to Get Started?
            </h2>
            <p class="text-primary-100 text-lg mb-8 max-w-2xl mx-auto">
                Join thousands of schools that have transformed their operations with EduVault Pro. Start your free trial
                today.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('contact') }}"
                    class="bg-white text-primary-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-50 transition-colors duration-200">
                    Start Free Trial
                </a>
                <a href="{{ route('contact') }}"
                    class="border-2 border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-white hover:text-primary-600 transition-colors duration-200">
                    Schedule Demo
                </a>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@endpush