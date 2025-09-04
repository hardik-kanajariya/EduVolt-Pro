@extends('layouts.guest')

@section('title', 'Contact Us - EduVault Pro')
@section('description', 'Get in touch with EduVault Pro team for product demos, support, or sales inquiries. We are here to help transform your school with our digital management solution.')
@section('keywords', 'contact EduVault Pro, school management system support, product demo, sales inquiry, customer support')

@section('content')
    <!-- Hero Section -->
    <section class="bg-gradient-to-br from-primary-50 to-blue-50 section-padding">
        <div class="container-custom text-center">
            <h1 class="heading-1 mb-6">
                Get in <span class="text-primary-600">Touch</span> with Us
            </h1>
            <p class="text-body max-w-3xl mx-auto mb-8">
                Ready to transform your school with EduVault Pro? Contact our team for a personalized demo, pricing
                information, or any questions about our comprehensive school management solution.
            </p>
        </div>
    </section>

    <!-- Contact Information -->
    <section class="section-padding bg-white">
        <div class="container-custom">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                <!-- Contact Form -->
                <div>
                    <h2 class="heading-2 mb-6">Send us a Message</h2>
                    <p class="text-body mb-8">
                        Fill out the form below and our team will get back to you within 24 hours.
                    </p>

                    <form class="space-y-6" method="POST" action="{{ route('contact') }}">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">First Name
                                    *</label>
                                <input type="text" id="first_name" name="first_name" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-200"
                                    placeholder="Enter your first name">
                            </div>
                            <div>
                                <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">Last Name
                                    *</label>
                                <input type="text" id="last_name" name="last_name" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-200"
                                    placeholder="Enter your last name">
                            </div>
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address *</label>
                            <input type="email" id="email" name="email" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-200"
                                placeholder="Enter your email address">
                        </div>

                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                            <input type="tel" id="phone" name="phone"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-200"
                                placeholder="Enter your phone number">
                        </div>

                        <div>
                            <label for="school_name"
                                class="block text-sm font-medium text-gray-700 mb-2">School/Organization Name</label>
                            <input type="text" id="school_name" name="school_name"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-200"
                                placeholder="Enter your school or organization name">
                        </div>

                        <div>
                            <label for="inquiry_type" class="block text-sm font-medium text-gray-700 mb-2">Inquiry Type
                                *</label>
                            <select id="inquiry_type" name="inquiry_type" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-200">
                                <option value="">Select inquiry type</option>
                                <option value="product_demo">Product Demo</option>
                                <option value="pricing">Pricing Information</option>
                                <option value="technical_support">Technical Support</option>
                                <option value="sales">Sales Inquiry</option>
                                <option value="partnership">Partnership</option>
                                <option value="other">Other</option>
                            </select>
                        </div>

                        <div>
                            <label for="message" class="block text-sm font-medium text-gray-700 mb-2">Message *</label>
                            <textarea id="message" name="message" rows="5" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-200"
                                placeholder="Tell us about your requirements and how we can help you..."></textarea>
                        </div>

                        <div class="flex items-start space-x-3">
                            <input type="checkbox" id="consent" name="consent" required
                                class="mt-1 w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                            <label for="consent" class="text-sm text-gray-600">
                                I agree to receive communications from EduVault Pro and understand that I can unsubscribe at
                                any time. *
                            </label>
                        </div>

                        <button type="submit" class="btn-primary w-full text-lg py-3">
                            Send Message
                        </button>
                    </form>
                </div>

                <!-- Contact Information -->
                <div>
                    <h2 class="heading-2 mb-6">Contact Information</h2>
                    <p class="text-body mb-8">
                        Connect with us through multiple channels. We're here to support your educational institution's
                        digital transformation journey.
                    </p>

                    <div class="space-y-8">
                        <!-- Office Address -->
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0 w-12 h-12 bg-primary-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                    </path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900 mb-2">Office Address</h3>
                                <p class="text-gray-600">123 Education Street<br>Tech City, TC 12345<br>United States</p>
                            </div>
                        </div>

                        <!-- Phone -->
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0 w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                                    </path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900 mb-2">Phone Support</h3>
                                <p class="text-gray-600">
                                    Sales: +1 (555) 123-4567<br>
                                    Support: +1 (555) 123-4568<br>
                                    Mon - Fri: 9AM - 6PM EST
                                </p>
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0 w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                    </path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900 mb-2">Email Support</h3>
                                <p class="text-gray-600">
                                    General: hello@eduvaultpro.com<br>
                                    Sales: sales@eduvaultpro.com<br>
                                    Support: support@eduvaultpro.com
                                </p>
                            </div>
                        </div>

                        <!-- Live Chat -->
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0 w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
                                    </path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900 mb-2">Live Chat</h3>
                                <p class="text-gray-600">
                                    Available on our website<br>
                                    Mon - Fri: 9AM - 6PM EST<br>
                                    Average response: 2 minutes
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Demo Button -->
                    <div class="mt-8 p-6 bg-primary-50 rounded-lg border border-primary-100">
                        <h3 class="font-semibold text-gray-900 mb-2">Need a Quick Demo?</h3>
                        <p class="text-gray-600 mb-4">See EduVault Pro in action with a personalized demo session.</p>
                        <button onclick="alert('Demo booking functionality will be implemented!')" class="btn-primary">
                            Book a Demo
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="section-padding bg-gray-50">
        <div class="container-custom">
            <div class="text-center mb-16">
                <h2 class="heading-2 mb-6">Frequently Asked Questions</h2>
                <p class="text-body max-w-3xl mx-auto">
                    Find answers to common questions about EduVault Pro. Can't find what you're looking for? Contact us
                    directly.
                </p>
            </div>

            <div class="max-w-4xl mx-auto" x-data="{ openFaq: null }">
                <div class="space-y-4">
                    <!-- FAQ Item 1 -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <button @click="openFaq = openFaq === 1 ? null : 1"
                            class="w-full text-left px-6 py-4 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-inset">
                            <div class="flex justify-between items-center">
                                <h3 class="font-semibold text-gray-900">How long does it take to implement EduVault Pro?
                                </h3>
                                <svg class="w-5 h-5 text-gray-500 transition-transform duration-200"
                                    :class="{ 'rotate-180': openFaq === 1 }" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </button>
                        <div x-show="openFaq === 1" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
                            x-transition:leave-end="opacity-0" class="px-6 pb-4">
                            <p class="text-gray-600">Implementation typically takes 2-4 weeks depending on your school size
                                and data migration requirements. Our team provides complete setup, data migration, and
                                training services.</p>
                        </div>
                    </div>

                    <!-- FAQ Item 2 -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <button @click="openFaq = openFaq === 2 ? null : 2"
                            class="w-full text-left px-6 py-4 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-inset">
                            <div class="flex justify-between items-center">
                                <h3 class="font-semibold text-gray-900">Is there a limit on the number of students?</h3>
                                <svg class="w-5 h-5 text-gray-500 transition-transform duration-200"
                                    :class="{ 'rotate-180': openFaq === 2 }" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </button>
                        <div x-show="openFaq === 2" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
                            x-transition:leave-end="opacity-0" class="px-6 pb-4">
                            <p class="text-gray-600">No, there's no limit on students, teachers, or classes. EduVault Pro
                                scales with your institution. Our pricing is based on the overall package, not per user.</p>
                        </div>
                    </div>

                    <!-- FAQ Item 3 -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <button @click="openFaq = openFaq === 3 ? null : 3"
                            class="w-full text-left px-6 py-4 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-inset">
                            <div class="flex justify-between items-center">
                                <h3 class="font-semibold text-gray-900">What kind of support do you provide?</h3>
                                <svg class="w-5 h-5 text-gray-500 transition-transform duration-200"
                                    :class="{ 'rotate-180': openFaq === 3 }" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </button>
                        <div x-show="openFaq === 3" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
                            x-transition:leave-end="opacity-0" class="px-6 pb-4">
                            <p class="text-gray-600">We provide comprehensive support including initial training, ongoing
                                technical support, regular updates, and dedicated customer success management. Support is
                                available via email, phone, and live chat.</p>
                        </div>
                    </div>

                    <!-- FAQ Item 4 -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <button @click="openFaq = openFaq === 4 ? null : 4"
                            class="w-full text-left px-6 py-4 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-inset">
                            <div class="flex justify-between items-center">
                                <h3 class="font-semibold text-gray-900">Can we migrate data from our existing system?</h3>
                                <svg class="w-5 h-5 text-gray-500 transition-transform duration-200"
                                    :class="{ 'rotate-180': openFaq === 4 }" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </button>
                        <div x-show="openFaq === 4" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
                            x-transition:leave-end="opacity-0" class="px-6 pb-4">
                            <p class="text-gray-600">Yes, we provide comprehensive data migration services. Our team can
                                help migrate student records, academic data, fee information, and other critical data from
                                your existing system to EduVault Pro.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@endpush