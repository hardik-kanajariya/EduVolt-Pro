@extends('layouts.guest')

@section('title', 'Terms of Service - EduVault Pro')
@section('description', 'Read EduVault Pro terms of service and user agreement. Understand your rights and responsibilities when using our educational management platform.')
@section('keywords', 'terms of service, user agreement, educational software terms, EduVault Pro legal, service agreement')

@section('content')
    <!-- Hero Section -->
    <section class="bg-gradient-to-br from-primary-50 to-blue-50 section-padding">
        <div class="container-custom text-center">
            <h1 class="heading-1 mb-6">
                Terms of <span class="text-primary-600">Service</span>
            </h1>
            <p class="text-body max-w-3xl mx-auto mb-4">
                These terms govern your use of EduVault Pro services. Please read them carefully as they contain important
                information about your rights and obligations.
            </p>
            <p class="text-sm text-gray-600">
                Last updated: {{ date('F d, Y') }}
            </p>
        </div>
    </section>

    <!-- Terms Content -->
    <section class="section-padding bg-white">
        <div class="container-custom">
            <div class="max-w-4xl mx-auto prose prose-lg">

                <!-- Introduction -->
                <div class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">1. Introduction and Acceptance</h2>
                    <p class="text-gray-700 leading-relaxed mb-4">
                        Welcome to EduVault Pro. These Terms of Service ("Terms") are a legal agreement between you ("User,"
                        "you," or "your") and EduVault Pro ("Company," "we," "us," or "our") regarding your use of our
                        educational management platform and related services.
                    </p>
                    <p class="text-gray-700 leading-relaxed">
                        By accessing or using our services, you agree to be bound by these Terms. If you do not agree to
                        these Terms, you may not use our services. These Terms apply to all users, including students,
                        teachers, parents, administrators, and institutional representatives.
                    </p>
                </div>

                <!-- Service Description -->
                <div class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">2. Service Description</h2>

                    <p class="text-gray-700 mb-4">EduVault Pro provides a comprehensive educational management platform that
                        includes:</p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <h3 class="font-semibold text-gray-900 mb-2">Core Services</h3>
                            <ul class="text-gray-700 text-sm space-y-1">
                                <li>• Student information management</li>
                                <li>• Academic record keeping</li>
                                <li>• Attendance tracking</li>
                                <li>• Grade management</li>
                                <li>• Communication tools</li>
                            </ul>
                        </div>

                        <div class="bg-green-50 p-4 rounded-lg">
                            <h3 class="font-semibold text-gray-900 mb-2">Additional Features</h3>
                            <ul class="text-gray-700 text-sm space-y-1">
                                <li>• Financial management</li>
                                <li>• Library management</li>
                                <li>• Examination systems</li>
                                <li>• Reporting and analytics</li>
                                <li>• Mobile applications</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- User Accounts and Registration -->
                <div class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">3. User Accounts and Registration</h2>

                    <div class="space-y-6">
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-3">3.1 Account Creation</h3>
                            <p class="text-gray-700 mb-3">To use our services, you may need to create an account. You agree
                                to:</p>
                            <ul class="list-disc pl-6 space-y-2 text-gray-700">
                                <li>Provide accurate, current, and complete information</li>
                                <li>Maintain and update your information as needed</li>
                                <li>Keep your account credentials secure and confidential</li>
                                <li>Notify us immediately of any unauthorized access</li>
                                <li>Accept responsibility for all activities under your account</li>
                            </ul>
                        </div>

                        <div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-3">3.2 Institutional Accounts</h3>
                            <p class="text-gray-700">
                                Educational institutions may create master accounts and manage sub-accounts for their users.
                                The institution is responsible for ensuring compliance with these Terms by all its users.
                            </p>
                        </div>

                        <div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-3">3.3 Account Termination</h3>
                            <p class="text-gray-700">
                                We reserve the right to suspend or terminate accounts that violate these Terms or engage in
                                harmful activities. You may terminate your account at any time by contacting our support
                                team.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Acceptable Use Policy -->
                <div class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">4. Acceptable Use Policy</h2>

                    <div class="bg-red-50 border border-red-200 p-6 rounded-lg mb-6">
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">4.1 Prohibited Activities</h3>
                        <p class="text-gray-700 mb-3">You agree NOT to:</p>
                        <ul class="list-disc pl-6 space-y-2 text-gray-700">
                            <li>Use the service for any illegal or unauthorized purpose</li>
                            <li>Violate any laws, regulations, or third-party rights</li>
                            <li>Upload or transmit harmful, offensive, or inappropriate content</li>
                            <li>Attempt to gain unauthorized access to our systems</li>
                            <li>Interfere with or disrupt our services</li>
                            <li>Use automated tools to access or interact with our platform</li>
                            <li>Reverse engineer, decompile, or disassemble our software</li>
                            <li>Share account credentials with unauthorized persons</li>
                        </ul>
                    </div>

                    <div class="bg-green-50 border border-green-200 p-6 rounded-lg">
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">4.2 Appropriate Use</h3>
                        <p class="text-gray-700 mb-3">You agree to:</p>
                        <ul class="list-disc pl-6 space-y-2 text-gray-700">
                            <li>Use the service only for legitimate educational purposes</li>
                            <li>Respect the privacy and rights of other users</li>
                            <li>Maintain professional and appropriate communication</li>
                            <li>Follow your institution's policies and guidelines</li>
                            <li>Report any security vulnerabilities or abuse</li>
                        </ul>
                    </div>
                </div>

                <!-- Data and Privacy -->
                <div class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">5. Data and Privacy</h2>

                    <div class="space-y-6">
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-3">5.1 Data Ownership</h3>
                            <p class="text-gray-700">
                                You retain ownership of all data you submit to our platform. We act as a data processor for
                                educational institutions and comply with applicable privacy laws including FERPA, GDPR, and
                                COPPA.
                            </p>
                        </div>

                        <div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-3">5.2 Data Use</h3>
                            <p class="text-gray-700">
                                We use your data solely to provide educational services and as outlined in our Privacy
                                Policy. We do not sell, rent, or share personal data with third parties for marketing
                                purposes.
                            </p>
                        </div>

                        <div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-3">5.3 Data Security</h3>
                            <p class="text-gray-700">
                                We implement industry-standard security measures to protect your data. However, no system is
                                completely secure, and you acknowledge the inherent risks of data transmission and storage.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Payment and Billing -->
                <div class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">6. Payment and Billing</h2>

                    <div class="space-y-6">
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-3">6.1 Subscription Fees</h3>
                            <p class="text-gray-700">
                                Our services are provided on a subscription basis. Fees are charged according to your
                                selected plan and billing cycle. All fees are non-refundable unless otherwise specified.
                            </p>
                        </div>

                        <div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-3">6.2 Payment Methods</h3>
                            <p class="text-gray-700">
                                We accept major credit cards, bank transfers, and purchase orders. You authorize us to
                                charge your selected payment method for all applicable fees.
                            </p>
                        </div>

                        <div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-3">6.3 Late Payments</h3>
                            <p class="text-gray-700">
                                Overdue accounts may be suspended or terminated. We may charge late fees and pursue
                                collection of unpaid amounts.
                            </p>
                        </div>

                        <div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-3">6.4 Price Changes</h3>
                            <p class="text-gray-700">
                                We may change our pricing with 30 days' advance notice. Price changes will not affect your
                                current billing cycle.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Intellectual Property -->
                <div class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">7. Intellectual Property</h2>

                    <div class="space-y-6">
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-3">7.1 Our Rights</h3>
                            <p class="text-gray-700">
                                EduVault Pro and all related software, content, and materials are protected by intellectual
                                property laws. We retain all rights, title, and interest in our platform.
                            </p>
                        </div>

                        <div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-3">7.2 License Grant</h3>
                            <p class="text-gray-700">
                                We grant you a limited, non-exclusive, non-transferable license to use our services for
                                their intended educational purposes during your subscription period.
                            </p>
                        </div>

                        <div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-3">7.3 User Content</h3>
                            <p class="text-gray-700">
                                You grant us a license to use, store, and process content you upload solely for providing
                                our services. You retain ownership of your content.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Service Availability -->
                <div class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">8. Service Availability and Support</h2>

                    <div class="space-y-6">
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-3">8.1 Uptime</h3>
                            <p class="text-gray-700">
                                We strive to maintain 99.9% uptime but do not guarantee uninterrupted service. Scheduled
                                maintenance will be announced in advance when possible.
                            </p>
                        </div>

                        <div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-3">8.2 Support</h3>
                            <p class="text-gray-700">
                                We provide technical support according to your subscription plan. Support is available via
                                email, phone, and live chat during business hours.
                            </p>
                        </div>

                        <div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-3">8.3 Updates and Changes</h3>
                            <p class="text-gray-700">
                                We may update our services, add new features, or modify existing functionality. We will
                                provide notice of significant changes that affect your use of the service.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Limitation of Liability -->
                <div class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">9. Limitation of Liability</h2>

                    <div class="bg-yellow-50 border border-yellow-200 p-6 rounded-lg">
                        <p class="text-gray-700 mb-4">
                            <strong>IMPORTANT:</strong> To the maximum extent permitted by law, EduVault Pro shall not be
                            liable for:
                        </p>
                        <ul class="list-disc pl-6 space-y-2 text-gray-700">
                            <li>Indirect, incidental, special, or consequential damages</li>
                            <li>Loss of profits, data, or business opportunities</li>
                            <li>Service interruptions or data breaches beyond our control</li>
                            <li>Third-party actions or content</li>
                            <li>User errors or misuse of the platform</li>
                        </ul>
                        <p class="text-gray-700 mt-4">
                            Our total liability shall not exceed the amount paid by you for the service in the 12 months
                            preceding the claim.
                        </p>
                    </div>
                </div>

                <!-- Indemnification -->
                <div class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">10. Indemnification</h2>

                    <p class="text-gray-700">
                        You agree to indemnify, defend, and hold harmless EduVault Pro from any claims, damages, losses, or
                        expenses arising from:
                    </p>
                    <ul class="list-disc pl-6 space-y-2 text-gray-700 mt-4">
                        <li>Your use or misuse of our services</li>
                        <li>Violation of these Terms or applicable laws</li>
                        <li>Infringement of third-party rights</li>
                        <li>Content you upload or share through our platform</li>
                    </ul>
                </div>

                <!-- Termination -->
                <div class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">11. Termination</h2>

                    <div class="space-y-6">
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-3">11.1 Termination by You</h3>
                            <p class="text-gray-700">
                                You may terminate your account at any time by providing written notice. Termination will be
                                effective at the end of your current billing period.
                            </p>
                        </div>

                        <div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-3">11.2 Termination by Us</h3>
                            <p class="text-gray-700">
                                We may terminate or suspend your account immediately for violations of these Terms,
                                non-payment, or other legitimate business reasons.
                            </p>
                        </div>

                        <div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-3">11.3 Effect of Termination</h3>
                            <p class="text-gray-700">
                                Upon termination, your access to the service will cease. We will provide a reasonable
                                opportunity to export your data before permanent deletion.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Governing Law -->
                <div class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">12. Governing Law and Dispute Resolution</h2>

                    <div class="space-y-4">
                        <p class="text-gray-700">
                            These Terms are governed by the laws of [State/Country], without regard to conflict of law
                            principles.
                        </p>

                        <p class="text-gray-700">
                            Any disputes arising from these Terms or your use of our services shall be resolved through
                            binding arbitration, except for claims that may be brought in small claims court.
                        </p>

                        <p class="text-gray-700">
                            You waive any right to participate in class action lawsuits or class-wide arbitration against
                            EduVault Pro.
                        </p>
                    </div>
                </div>

                <!-- General Provisions -->
                <div class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">13. General Provisions</h2>

                    <div class="space-y-6">
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-3">13.1 Entire Agreement</h3>
                            <p class="text-gray-700">
                                These Terms, along with our Privacy Policy, constitute the entire agreement between you and
                                EduVault Pro regarding the use of our services.
                            </p>
                        </div>

                        <div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-3">13.2 Severability</h3>
                            <p class="text-gray-700">
                                If any provision of these Terms is found to be unenforceable, the remaining provisions will
                                remain in full force and effect.
                            </p>
                        </div>

                        <div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-3">13.3 Waiver</h3>
                            <p class="text-gray-700">
                                Our failure to enforce any provision does not constitute a waiver of our rights to enforce
                                that provision in the future.
                            </p>
                        </div>

                        <div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-3">13.4 Assignment</h3>
                            <p class="text-gray-700">
                                You may not assign these Terms without our prior written consent. We may assign our rights
                                and obligations at any time.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Changes to Terms -->
                <div class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">14. Changes to These Terms</h2>

                    <p class="text-gray-700 mb-4">
                        We may update these Terms from time to time. We will provide notice of material changes by:
                    </p>
                    <ul class="list-disc pl-6 space-y-2 text-gray-700 mb-4">
                        <li>Posting the updated Terms on our website</li>
                        <li>Sending email notifications to account holders</li>
                        <li>Providing in-app notifications</li>
                    </ul>
                    <p class="text-gray-700">
                        Your continued use of our services after the effective date of changes constitutes acceptance of the
                        updated Terms.
                    </p>
                </div>

                <!-- Contact Information -->
                <div class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">15. Contact Information</h2>

                    <p class="text-gray-700 mb-4">
                        If you have questions about these Terms, please contact us:
                    </p>

                    <div class="bg-gray-50 p-6 rounded-lg">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <h3 class="font-semibold text-gray-900 mb-2">Legal Department</h3>
                                <p class="text-gray-700">
                                    Email: legal@eduvaultpro.com<br>
                                    Phone: +1 (555) 123-4567
                                </p>
                            </div>

                            <div>
                                <h3 class="font-semibold text-gray-900 mb-2">Mailing Address</h3>
                                <p class="text-gray-700">
                                    EduVault Pro Legal Department<br>
                                    123 Education Street<br>
                                    Tech City, TC 12345, USA
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Acknowledgment -->
                <div class="bg-primary-50 border border-primary-200 p-6 rounded-lg">
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Acknowledgment</h3>
                    <p class="text-gray-700">
                        BY USING EDUVAULT PRO SERVICES, YOU ACKNOWLEDGE THAT YOU HAVE READ, UNDERSTOOD, AND AGREE TO BE
                        BOUND BY THESE TERMS OF SERVICE.
                    </p>
                </div>

            </div>
        </div>
    </section>
@endsection