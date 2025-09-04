@extends('layouts.guest')

@section('title', 'About Us - EduVault Pro')
@section('description', 'Learn about EduVault Pro mission to transform education through innovative school management technology. Meet our team and discover our journey.')
@section('keywords', 'about EduVault Pro, education technology company, school management system, team, mission, vision')

@section('content')
    <!-- Hero Section -->
    <section class="bg-gradient-to-br from-primary-50 to-blue-50 section-padding">
        <div class="container-custom">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div>
                    <h1 class="heading-1 mb-6">
                        Transforming <span class="text-primary-600">Education</span> Through Technology
                    </h1>
                    <p class="text-body mb-8">
                        EduVault Pro was born from a simple belief: every educational institution deserves access to
                        powerful, easy-to-use technology that enhances learning and simplifies administration.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="{{ route('contact') }}" class="btn-primary text-center">
                            Get In Touch
                        </a>
                        <a href="{{ route('features') }}" class="btn-secondary text-center">
                            See Our Features
                        </a>
                    </div>
                </div>
                <div class="relative">
                    <div class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
                        <div class="text-center">
                            <div
                                class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                                    </path>
                                </svg>
                            </div>
                            <h3 class="font-bold text-gray-900 mb-2">Our Mission</h3>
                            <p class="text-gray-600">Empowering educational institutions with comprehensive, user-friendly
                                technology solutions that enhance learning outcomes and operational efficiency.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Company Story -->
    <section class="section-padding bg-white">
        <div class="container-custom">
            <div class="text-center mb-16">
                <h2 class="heading-2 mb-6">Our Story</h2>
                <p class="text-body max-w-3xl mx-auto">
                    Founded in 2018 by a team of educators and technologists, EduVault Pro emerged from firsthand experience
                    with the challenges facing modern educational institutions.
                </p>
            </div>

            <div class="max-w-4xl mx-auto">
                <div class="space-y-12">
                    <!-- Timeline Item 1 -->
                    <div class="flex flex-col md:flex-row gap-8 items-center">
                        <div class="md:w-1/3">
                            <div
                                class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mx-auto md:mx-0">
                                <span class="text-2xl font-bold text-primary-600">2018</span>
                            </div>
                        </div>
                        <div class="md:w-2/3 text-center md:text-left">
                            <h3 class="text-xl font-bold text-gray-900 mb-3">The Beginning</h3>
                            <p class="text-gray-600">
                                Started as a small project to help local schools manage their administrative tasks more
                                efficiently. Our founders, having worked in education for over a decade, understood the pain
                                points of traditional management systems.
                            </p>
                        </div>
                    </div>

                    <!-- Timeline Item 2 -->
                    <div class="flex flex-col md:flex-row-reverse gap-8 items-center">
                        <div class="md:w-1/3">
                            <div
                                class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto md:mx-0">
                                <span class="text-2xl font-bold text-green-600">2020</span>
                            </div>
                        </div>
                        <div class="md:w-2/3 text-center md:text-right">
                            <h3 class="text-xl font-bold text-gray-900 mb-3">Major Expansion</h3>
                            <p class="text-gray-600">
                                The global shift to digital education accelerated our growth. We expanded our platform to
                                support remote learning, hybrid models, and comprehensive digital workflows, helping schools
                                adapt to new realities.
                            </p>
                        </div>
                    </div>

                    <!-- Timeline Item 3 -->
                    <div class="flex flex-col md:flex-row gap-8 items-center">
                        <div class="md:w-1/3">
                            <div
                                class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto md:mx-0">
                                <span class="text-2xl font-bold text-blue-600">2022</span>
                            </div>
                        </div>
                        <div class="md:w-2/3 text-center md:text-left">
                            <h3 class="text-xl font-bold text-gray-900 mb-3">Global Reach</h3>
                            <p class="text-gray-600">
                                Reached 500+ schools across multiple countries. Launched advanced features including
                                AI-powered analytics, biometric attendance, and comprehensive financial management modules.
                            </p>
                        </div>
                    </div>

                    <!-- Timeline Item 4 -->
                    <div class="flex flex-col md:flex-row-reverse gap-8 items-center">
                        <div class="md:w-1/3">
                            <div
                                class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto md:mx-0">
                                <span class="text-2xl font-bold text-purple-600">2024</span>
                            </div>
                        </div>
                        <div class="md:w-2/3 text-center md:text-right">
                            <h3 class="text-xl font-bold text-gray-900 mb-3">Innovation Continues</h3>
                            <p class="text-gray-600">
                                Today, we continue to innovate with cutting-edge features, enhanced security, and deeper
                                integrations. Our commitment to educational excellence drives every feature we build.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Values Section -->
    <section class="section-padding bg-gray-50">
        <div class="container-custom">
            <div class="text-center mb-16">
                <h2 class="heading-2 mb-6">Our Core Values</h2>
                <p class="text-body max-w-3xl mx-auto">
                    These values guide everything we do, from product development to customer support.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Value 1 -->
                <div class="text-center p-8 bg-white rounded-xl shadow-sm border border-gray-100">
                    <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Student-First</h3>
                    <p class="text-gray-600">
                        Every feature we build is designed with students' success and wellbeing at the center. Their
                        educational journey is our top priority.
                    </p>
                </div>

                <!-- Value 2 -->
                <div class="text-center p-8 bg-white rounded-xl shadow-sm border border-gray-100">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Simplicity</h3>
                    <p class="text-gray-600">
                        Complex problems deserve elegant solutions. We believe powerful technology should be intuitive and
                        accessible to everyone.
                    </p>
                </div>

                <!-- Value 3 -->
                <div class="text-center p-8 bg-white rounded-xl shadow-sm border border-gray-100">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Innovation</h3>
                    <p class="text-gray-600">
                        We constantly push boundaries, embrace new technologies, and find creative ways to solve educational
                        challenges.
                    </p>
                </div>

                <!-- Value 4 -->
                <div class="text-center p-8 bg-white rounded-xl shadow-sm border border-gray-100">
                    <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Collaboration</h3>
                    <p class="text-gray-600">
                        Education is a team effort. We build tools that bring students, teachers, parents, and
                        administrators together.
                    </p>
                </div>

                <!-- Value 5 -->
                <div class="text-center p-8 bg-white rounded-xl shadow-sm border border-gray-100">
                    <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Security</h3>
                    <p class="text-gray-600">
                        We protect student data with enterprise-grade security, ensuring privacy and compliance with
                        educational regulations.
                    </p>
                </div>

                <!-- Value 6 -->
                <div class="text-center p-8 bg-white rounded-xl shadow-sm border border-gray-100">
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Excellence</h3>
                    <p class="text-gray-600">
                        We strive for excellence in everything we do, from code quality to customer service, because
                        education deserves the best.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Team Section -->
    <section class="section-padding bg-white">
        <div class="container-custom">
            <div class="text-center mb-16">
                <h2 class="heading-2 mb-6">Meet Our Leadership Team</h2>
                <p class="text-body max-w-3xl mx-auto">
                    Our diverse team brings together expertise in education, technology, and business to create solutions
                    that truly make a difference.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 max-w-6xl mx-auto">
                <!-- Team Member 1 -->
                <div class="text-center group">
                    <div class="relative mb-6">
                        <div
                            class="w-32 h-32 bg-gradient-to-br from-primary-400 to-primary-600 rounded-full mx-auto flex items-center justify-center">
                            <span class="text-3xl font-bold text-white">AS</span>
                        </div>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Dr. Sarah Anderson</h3>
                    <p class="text-primary-600 font-medium mb-3">CEO & Co-Founder</p>
                    <p class="text-gray-600 text-sm">
                        Former principal with 15+ years in education. PhD in Educational Technology from Stanford.
                        Passionate about democratizing quality education through technology.
                    </p>
                </div>

                <!-- Team Member 2 -->
                <div class="text-center group">
                    <div class="relative mb-6">
                        <div
                            class="w-32 h-32 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full mx-auto flex items-center justify-center">
                            <span class="text-3xl font-bold text-white">MC</span>
                        </div>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Michael Chen</h3>
                    <p class="text-primary-600 font-medium mb-3">CTO & Co-Founder</p>
                    <p class="text-gray-600 text-sm">
                        Former senior engineer at Google. MS in Computer Science from MIT. Expert in scalable systems and
                        educational software architecture.
                    </p>
                </div>

                <!-- Team Member 3 -->
                <div class="text-center group">
                    <div class="relative mb-6">
                        <div
                            class="w-32 h-32 bg-gradient-to-br from-green-400 to-green-600 rounded-full mx-auto flex items-center justify-center">
                            <span class="text-3xl font-bold text-white">EP</span>
                        </div>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Emily Rodriguez</h3>
                    <p class="text-primary-600 font-medium mb-3">Head of Product</p>
                    <p class="text-gray-600 text-sm">
                        Former product manager at educational institutions. MS in Human-Computer Interaction. Focuses on
                        user experience and accessibility in educational technology.
                    </p>
                </div>

                <!-- Team Member 4 -->
                <div class="text-center group">
                    <div class="relative mb-6">
                        <div
                            class="w-32 h-32 bg-gradient-to-br from-purple-400 to-purple-600 rounded-full mx-auto flex items-center justify-center">
                            <span class="text-3xl font-bold text-white">DJ</span>
                        </div>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">David Johnson</h3>
                    <p class="text-primary-600 font-medium mb-3">Head of Customer Success</p>
                    <p class="text-gray-600 text-sm">
                        Former school administrator with deep understanding of institutional needs. Ensures our customers
                        achieve maximum value from EduVault Pro.
                    </p>
                </div>

                <!-- Team Member 5 -->
                <div class="text-center group">
                    <div class="relative mb-6">
                        <div
                            class="w-32 h-32 bg-gradient-to-br from-orange-400 to-orange-600 rounded-full mx-auto flex items-center justify-center">
                            <span class="text-3xl font-bold text-white">LK</span>
                        </div>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Lisa Kim</h3>
                    <p class="text-primary-600 font-medium mb-3">Head of Engineering</p>
                    <p class="text-gray-600 text-sm">
                        Senior software architect with expertise in distributed systems. Leads our engineering team in
                        building robust, scalable solutions.
                    </p>
                </div>

                <!-- Team Member 6 -->
                <div class="text-center group">
                    <div class="relative mb-6">
                        <div
                            class="w-32 h-32 bg-gradient-to-br from-teal-400 to-teal-600 rounded-full mx-auto flex items-center justify-center">
                            <span class="text-3xl font-bold text-white">RM</span>
                        </div>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Robert Miller</h3>
                    <p class="text-primary-600 font-medium mb-3">Head of Security</p>
                    <p class="text-gray-600 text-sm">
                        Cybersecurity expert with focus on educational data protection. Ensures our platform meets the
                        highest security and compliance standards.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Statistics Section -->
    <section class="section-padding bg-primary-600">
        <div class="container-custom">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-white mb-6">
                    Our Impact in Numbers
                </h2>
                <p class="text-primary-100 max-w-3xl mx-auto">
                    See how EduVault Pro is making a difference in educational institutions worldwide.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="text-center">
                    <div class="text-4xl md:text-5xl font-bold text-white mb-2">500+</div>
                    <div class="text-primary-100">Schools Served</div>
                </div>
                <div class="text-center">
                    <div class="text-4xl md:text-5xl font-bold text-white mb-2">50K+</div>
                    <div class="text-primary-100">Students Managed</div>
                </div>
                <div class="text-center">
                    <div class="text-4xl md:text-5xl font-bold text-white mb-2">99.9%</div>
                    <div class="text-primary-100">System Uptime</div>
                </div>
                <div class="text-center">
                    <div class="text-4xl md:text-5xl font-bold text-white mb-2">24/7</div>
                    <div class="text-primary-100">Support Available</div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="section-padding bg-gray-50">
        <div class="container-custom text-center">
            <h2 class="heading-2 mb-6">Ready to Transform Your Institution?</h2>
            <p class="text-body max-w-3xl mx-auto mb-8">
                Join hundreds of schools that have already transformed their operations with EduVault Pro. Let's discuss how
                we can help your institution succeed.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('contact') }}" class="btn-primary">
                    Schedule a Demo
                </a>
                <a href="{{ route('pricing') }}" class="btn-secondary">
                    View Pricing
                </a>
            </div>
        </div>
    </section>
@endsection