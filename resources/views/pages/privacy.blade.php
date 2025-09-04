@extends('layouts.guest')

@section('title', 'Privacy Policy - EduVault Pro')
@section('description', 'Learn how EduVault Pro protects your privacy and handles personal data. Our comprehensive privacy policy ensures transparency and compliance.')
@section('keywords', 'privacy policy, data protection, student privacy, GDPR compliance, educational data security')

@section('content')
    <!-- Hero Section -->
    <section class="bg-gradient-to-br from-primary-50 to-blue-50 section-padding">
        <div class="container-custom text-center">
            <h1 class="heading-1 mb-6">
                Privacy <span class="text-primary-600">Policy</span>
            </h1>
            <p class="text-body max-w-3xl mx-auto mb-4">
                We are committed to protecting your privacy and ensuring the security of your personal information. This
                policy explains how we collect, use, and safeguard your data.
            </p>
            <p class="text-sm text-gray-600">
                Last updated: {{ date('F d, Y') }}
            </p>
        </div>
    </section>

    <!-- Privacy Content -->
    <section class="section-padding bg-white">
        <div class="container-custom">
            <div class="max-w-4xl mx-auto prose prose-lg">

                <!-- Overview -->
                <div class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Overview</h2>
                    <p class="text-gray-700 leading-relaxed">
                        EduVault Pro ("we," "our," or "us") respects your privacy and is committed to protecting your
                        personal data. This privacy policy will inform you about how we look after your personal data when
                        you visit our website or use our services and tell you about your privacy rights and how the law
                        protects you.
                    </p>
                </div>

                <!-- Information We Collect -->
                <div class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Information We Collect</h2>

                    <div class="space-y-6">
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-3">Personal Information</h3>
                            <p class="text-gray-700 mb-3">We may collect the following types of personal information:</p>
                            <ul class="list-disc pl-6 space-y-2 text-gray-700">
                                <li>Student information (names, contact details, academic records, attendance data)</li>
                                <li>Staff information (teacher and administrator details, employment records)</li>
                                <li>Parent/guardian contact information</li>
                                <li>Account credentials and authentication data</li>
                                <li>Communication records (emails, messages, support tickets)</li>
                                <li>Payment and billing information</li>
                            </ul>
                        </div>

                        <div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-3">Technical Information</h3>
                            <p class="text-gray-700 mb-3">We automatically collect certain technical information:</p>
                            <ul class="list-disc pl-6 space-y-2 text-gray-700">
                                <li>IP addresses and device identifiers</li>
                                <li>Browser type and version</li>
                                <li>Operating system and platform</li>
                                <li>Login times and usage patterns</li>
                                <li>System logs and error reports</li>
                            </ul>
                        </div>

                        <div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-3">Educational Records</h3>
                            <p class="text-gray-700 mb-3">As an educational service provider, we handle:</p>
                            <ul class="list-disc pl-6 space-y-2 text-gray-700">
                                <li>Academic performance data and grades</li>
                                <li>Attendance and behavioral records</li>
                                <li>Assessment and examination results</li>
                                <li>Individual Education Plans (IEPs) where applicable</li>
                                <li>Health and medical information (when necessary for educational services)</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- How We Use Your Information -->
                <div class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">How We Use Your Information</h2>

                    <div class="space-y-6">
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-3">Educational Services</h3>
                            <ul class="list-disc pl-6 space-y-2 text-gray-700">
                                <li>Providing educational management and administrative services</li>
                                <li>Facilitating communication between students, teachers, and parents</li>
                                <li>Tracking academic progress and generating reports</li>
                                <li>Managing attendance and scheduling</li>
                                <li>Processing fees and financial transactions</li>
                            </ul>
                        </div>

                        <div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-3">Platform Improvement</h3>
                            <ul class="list-disc pl-6 space-y-2 text-gray-700">
                                <li>Analyzing usage patterns to improve our services</li>
                                <li>Developing new features and functionality</li>
                                <li>Ensuring system security and preventing fraud</li>
                                <li>Providing technical support and troubleshooting</li>
                            </ul>
                        </div>

                        <div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-3">Legal and Compliance</h3>
                            <ul class="list-disc pl-6 space-y-2 text-gray-700">
                                <li>Complying with legal obligations and regulations</li>
                                <li>Protecting our rights and interests</li>
                                <li>Responding to legal requests and court orders</li>
                                <li>Maintaining records as required by education authorities</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Data Sharing and Disclosure -->
                <div class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Data Sharing and Disclosure</h2>

                    <p class="text-gray-700 mb-4">We do not sell, trade, or rent your personal information. We may share
                        your data only in the following circumstances:</p>

                    <div class="space-y-4">
                        <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                            <h3 class="font-semibold text-gray-900 mb-2">With Educational Institutions</h3>
                            <p class="text-gray-700">We share data with the schools and educational institutions that use
                                our services for legitimate educational purposes.</p>
                        </div>

                        <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                            <h3 class="font-semibold text-gray-900 mb-2">Service Providers</h3>
                            <p class="text-gray-700">We may share data with trusted third-party service providers who assist
                                us in operating our platform, subject to strict confidentiality agreements.</p>
                        </div>

                        <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                            <h3 class="font-semibold text-gray-900 mb-2">Legal Requirements</h3>
                            <p class="text-gray-700">We may disclose information when required by law, court order, or to
                                protect our rights, property, or safety.</p>
                        </div>

                        <div class="bg-red-50 p-4 rounded-lg border border-red-200">
                            <h3 class="font-semibold text-gray-900 mb-2">Emergency Situations</h3>
                            <p class="text-gray-700">We may share information in emergency situations to protect the health
                                and safety of students or staff.</p>
                        </div>
                    </div>
                </div>

                <!-- Data Security -->
                <div class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Data Security</h2>

                    <p class="text-gray-700 mb-4">We implement comprehensive security measures to protect your data:</p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <h3 class="font-semibold text-gray-900 mb-3">Technical Safeguards</h3>
                            <ul class="list-disc pl-5 space-y-1 text-gray-700">
                                <li>256-bit SSL encryption</li>
                                <li>Secure data centers</li>
                                <li>Regular security audits</li>
                                <li>Multi-factor authentication</li>
                                <li>Firewall protection</li>
                            </ul>
                        </div>

                        <div class="bg-gray-50 p-6 rounded-lg">
                            <h3 class="font-semibold text-gray-900 mb-3">Administrative Safeguards</h3>
                            <ul class="list-disc pl-5 space-y-1 text-gray-700">
                                <li>Access controls and permissions</li>
                                <li>Employee background checks</li>
                                <li>Privacy training programs</li>
                                <li>Incident response procedures</li>
                                <li>Regular compliance reviews</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Student Privacy Rights -->
                <div class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Student Privacy Rights (FERPA Compliance)</h2>

                    <p class="text-gray-700 mb-4">We comply with the Family Educational Rights and Privacy Act (FERPA) and
                        other applicable privacy laws:</p>

                    <div class="space-y-4">
                        <div class="border-l-4 border-primary-500 pl-4">
                            <h3 class="font-semibold text-gray-900 mb-2">Right to Access</h3>
                            <p class="text-gray-700">Students and parents have the right to access their educational
                                records.</p>
                        </div>

                        <div class="border-l-4 border-primary-500 pl-4">
                            <h3 class="font-semibold text-gray-900 mb-2">Right to Correct</h3>
                            <p class="text-gray-700">Students and parents can request corrections to inaccurate records.</p>
                        </div>

                        <div class="border-l-4 border-primary-500 pl-4">
                            <h3 class="font-semibold text-gray-900 mb-2">Right to Consent</h3>
                            <p class="text-gray-700">Consent is required before disclosing educational records to third
                                parties (with certain exceptions).</p>
                        </div>

                        <div class="border-l-4 border-primary-500 pl-4">
                            <h3 class="font-semibold text-gray-900 mb-2">Right to File Complaints</h3>
                            <p class="text-gray-700">Students and parents can file complaints with the U.S. Department of
                                Education regarding privacy violations.</p>
                        </div>
                    </div>
                </div>

                <!-- International Data Transfers -->
                <div class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">International Data Transfers</h2>

                    <p class="text-gray-700 mb-4">
                        Your data may be transferred to and processed in countries other than your own. We ensure
                        appropriate safeguards are in place for such transfers, including:
                    </p>

                    <ul class="list-disc pl-6 space-y-2 text-gray-700">
                        <li>Adequacy decisions by relevant authorities</li>
                        <li>Standard contractual clauses</li>
                        <li>Binding corporate rules</li>
                        <li>Certification schemes</li>
                    </ul>
                </div>

                <!-- Data Retention -->
                <div class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Data Retention</h2>

                    <p class="text-gray-700 mb-4">We retain your data only as long as necessary for:</p>

                    <ul class="list-disc pl-6 space-y-2 text-gray-700">
                        <li>Providing educational services</li>
                        <li>Complying with legal obligations</li>
                        <li>Resolving disputes</li>
                        <li>Enforcing our agreements</li>
                    </ul>

                    <p class="text-gray-700 mt-4">
                        Generally, student records are retained for 7 years after graduation or withdrawal, unless longer
                        retention is required by law.
                    </p>
                </div>

                <!-- Your Rights -->
                <div class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Your Rights</h2>

                    <p class="text-gray-700 mb-4">Depending on your location, you may have the following rights:</p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-primary-50 p-4 rounded-lg">
                            <h3 class="font-semibold text-gray-900 mb-2">Right to Know</h3>
                            <p class="text-gray-700 text-sm">Request information about data collection and use</p>
                        </div>

                        <div class="bg-primary-50 p-4 rounded-lg">
                            <h3 class="font-semibold text-gray-900 mb-2">Right to Access</h3>
                            <p class="text-gray-700 text-sm">Request a copy of your personal data</p>
                        </div>

                        <div class="bg-primary-50 p-4 rounded-lg">
                            <h3 class="font-semibold text-gray-900 mb-2">Right to Rectify</h3>
                            <p class="text-gray-700 text-sm">Correct inaccurate personal data</p>
                        </div>

                        <div class="bg-primary-50 p-4 rounded-lg">
                            <h3 class="font-semibold text-gray-900 mb-2">Right to Delete</h3>
                            <p class="text-gray-700 text-sm">Request deletion of your personal data</p>
                        </div>

                        <div class="bg-primary-50 p-4 rounded-lg">
                            <h3 class="font-semibold text-gray-900 mb-2">Right to Portability</h3>
                            <p class="text-gray-700 text-sm">Request transfer of your data</p>
                        </div>

                        <div class="bg-primary-50 p-4 rounded-lg">
                            <h3 class="font-semibold text-gray-900 mb-2">Right to Object</h3>
                            <p class="text-gray-700 text-sm">Object to certain data processing</p>
                        </div>
                    </div>
                </div>

                <!-- Cookies and Tracking -->
                <div class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Cookies and Tracking Technologies</h2>

                    <p class="text-gray-700 mb-4">We use cookies and similar technologies to:</p>

                    <ul class="list-disc pl-6 space-y-2 text-gray-700 mb-4">
                        <li>Remember your login and preferences</li>
                        <li>Analyze how you use our services</li>
                        <li>Improve our platform's performance</li>
                        <li>Provide personalized experiences</li>
                    </ul>

                    <p class="text-gray-700">
                        You can manage cookie preferences through your browser settings. However, disabling certain cookies
                        may affect platform functionality.
                    </p>
                </div>

                <!-- Third-Party Links -->
                <div class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Third-Party Links</h2>

                    <p class="text-gray-700">
                        Our platform may contain links to third-party websites or services. We are not responsible for the
                        privacy practices of these external sites. We encourage you to review their privacy policies before
                        providing any personal information.
                    </p>
                </div>

                <!-- Changes to Privacy Policy -->
                <div class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Changes to This Privacy Policy</h2>

                    <p class="text-gray-700">
                        We may update this privacy policy from time to time. We will notify you of any significant changes
                        by email or through our platform. Your continued use of our services after such modifications
                        constitutes acceptance of the updated policy.
                    </p>
                </div>

                <!-- Contact Information -->
                <div class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Contact Us</h2>

                    <p class="text-gray-700 mb-4">
                        If you have questions about this privacy policy or our data practices, please contact us:
                    </p>

                    <div class="bg-gray-50 p-6 rounded-lg">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <h3 class="font-semibold text-gray-900 mb-2">General Inquiries</h3>
                                <p class="text-gray-700">
                                    Email: privacy@eduvaultpro.com<br>
                                    Phone: +1 (555) 123-4567
                                </p>
                            </div>

                            <div>
                                <h3 class="font-semibold text-gray-900 mb-2">Data Protection Officer</h3>
                                <p class="text-gray-700">
                                    Email: dpo@eduvaultpro.com<br>
                                    Address: 123 Education Street<br>
                                    Tech City, TC 12345, USA
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
@endsection