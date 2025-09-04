@extends('layouts.guest')

@section('title', 'Features - EduVault Pro School Management System')
@section('description', 'Discover comprehensive features of EduVault Pro including student management, attendance tracking, fee management, library system, exam management, and powerful analytics tools.')
@section('keywords', 'school management features, student management system, attendance tracking, fee management, library management, exam system, educational software features')

@section('content')
<!-- Hero Section -->
<section class="bg-gradient-to-br from-primary-50 to-blue-50 section-padding">
    <div class="container-custom text-center">
        <h1 class="heading-1 mb-6">
            Comprehensive Features for <span class="text-primary-600">Modern Schools</span>
        </h1>
        <p class="text-body max-w-3xl mx-auto mb-8">
            EduVault Pro offers a complete suite of features designed to streamline every aspect of school management, from admission to graduation.
        </p>
        <div class="flex flex-wrap justify-center gap-4">
            <a href="#student-management" class="btn-primary">Student Management</a>
            <a href="#academic-features" class="btn-secondary">Academic Features</a>
            <a href="#financial-management" class="btn-secondary">Financial Management</a>
        </div>
    </div>
</section>

<!-- Student Management -->
<section id="student-management" class="section-padding bg-white">
    <div class="container-custom">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div>
                <h2 class="heading-2 mb-6">
                    <span class="text-primary-600">Student Management</span> Made Simple
                </h2>
                <p class="text-body mb-8">
                    Comprehensive student information system with complete profile management, enrollment tracking, and parent communication tools.
                </p>
                
                <div class="space-y-6">
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0 w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 mb-2">Complete Student Profiles</h3>
                            <p class="text-gray-600">Detailed student information including personal details, academic history, medical records, and emergency contacts.</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0 w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 mb-2">Enrollment Management</h3>
                            <p class="text-gray-600">Streamlined admission process with online applications, document management, and automated enrollment workflows.</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0 w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 mb-2">Parent Communication</h3>
                            <p class="text-gray-600">Integrated parent portal with real-time updates, messaging system, and progress tracking for better engagement.</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="bg-gradient-to-br from-blue-50 to-purple-50 p-8 rounded-2xl">
                <div class="bg-white rounded-xl p-6 shadow-lg">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="font-semibold text-gray-900">Student Profile</h4>
                        <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">Active</span>
                    </div>
                    <div class="space-y-3">
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                <span class="text-blue-600 font-semibold">JS</span>
                            </div>
                            <div>
                                <div class="font-medium">John Smith</div>
                                <div class="text-sm text-gray-500">Student ID: STU001247</div>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <div class="text-gray-500">Class</div>
                                <div class="font-medium">Grade 10-A</div>
                            </div>
                            <div>
                                <div class="text-gray-500">Roll No.</div>
                                <div class="font-medium">15</div>
                            </div>
                            <div>
                                <div class="text-gray-500">Attendance</div>
                                <div class="font-medium text-green-600">95.5%</div>
                            </div>
                            <div>
                                <div class="text-gray-500">Grade</div>
                                <div class="font-medium text-blue-600">A+</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Attendance Tracking -->
<section id="attendance-tracking" class="section-padding bg-gray-50">
    <div class="container-custom">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div class="order-2 lg:order-1">
                <div class="bg-gradient-to-br from-green-50 to-blue-50 p-8 rounded-2xl">
                    <div class="bg-white rounded-xl p-6 shadow-lg">
                        <h4 class="font-semibold text-gray-900 mb-4">Daily Attendance</h4>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-sm">Total Students</span>
                                <span class="font-semibold">42</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm">Present</span>
                                <span class="font-semibold text-green-600">39</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm">Absent</span>
                                <span class="font-semibold text-red-600">3</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm">Attendance Rate</span>
                                <span class="font-semibold text-blue-600">92.9%</span>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="bg-gray-200 rounded-full h-2">
                                <div class="bg-green-500 rounded-full h-2" style="width: 92.9%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="order-1 lg:order-2">
                <h2 class="heading-2 mb-6">
                    Smart <span class="text-primary-600">Attendance Tracking</span>
                </h2>
                <p class="text-body mb-8">
                    Efficient attendance management with real-time tracking, automated reports, and instant parent notifications for better student monitoring.
                </p>
                
                <div class="space-y-6">
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0 w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 mb-2">Real-time Tracking</h3>
                            <p class="text-gray-600">Mark attendance with one click and get instant updates across all platforms including parent portals.</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0 w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 mb-2">Automated Reports</h3>
                            <p class="text-gray-600">Generate comprehensive attendance reports with detailed analytics and insights for better decision making.</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0 w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 mb-2">Parent Notifications</h3>
                            <p class="text-gray-600">Automatic email and SMS notifications to parents when students are absent or late.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Academic Features -->
<section id="academic-features" class="section-padding bg-white">
    <div class="container-custom">
        <div class="text-center mb-16">
            <h2 class="heading-2 mb-6">
                Complete <span class="text-primary-600">Academic Management</span>
            </h2>
            <p class="text-body max-w-3xl mx-auto">
                From timetable management to exam scheduling, handle all academic activities with ease and efficiency.
            </p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Timetable Management -->
            <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <h3 class="heading-3 mb-3">Timetable Management</h3>
                <p class="text-gray-600 mb-4">Create and manage class schedules with conflict detection and automatic teacher allocation.</p>
                <ul class="text-sm text-gray-600 space-y-1">
                    <li>• Visual drag-and-drop interface</li>
                    <li>• Conflict detection system</li>
                    <li>• Mobile-responsive view</li>
                    <li>• PDF export functionality</li>
                </ul>
            </div>
            
            <!-- Assignment Management -->
            <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <h3 class="heading-3 mb-3">Assignment System</h3>
                <p class="text-gray-600 mb-4">Digital assignment distribution and submission with automated grading capabilities.</p>
                <ul class="text-sm text-gray-600 space-y-1">
                    <li>• Online submission portal</li>
                    <li>• Plagiarism detection</li>
                    <li>• Automated reminders</li>
                    <li>• Bulk grading tools</li>
                </ul>
            </div>
            
            <!-- Exam Management -->
            <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                    </svg>
                </div>
                <h3 class="heading-3 mb-3">Exam Management</h3>
                <p class="text-gray-600 mb-4">Comprehensive exam scheduling and result management with automated report generation.</p>
                <ul class="text-sm text-gray-600 space-y-1">
                    <li>• Exam scheduling system</li>
                    <li>• Hall ticket generation</li>
                    <li>• Marks entry interface</li>
                    <li>• Result analytics</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Financial Management -->
<section id="financial-management" class="section-padding bg-gray-50">
    <div class="container-custom">
        <div class="text-center mb-16">
            <h2 class="heading-2 mb-6">
                Smart <span class="text-primary-600">Financial Management</span>
            </h2>
            <p class="text-body max-w-3xl mx-auto">
                Comprehensive fee management system with automated calculations, receipt generation, and detailed financial reporting.
            </p>
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div>
                <div class="space-y-6">
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0 w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 mb-2">Fee Structure Management</h3>
                            <p class="text-gray-600">Configure flexible fee structures with support for installments, discounts, and scholarships.</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0 w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 mb-2">Automated Receipt Generation</h3>
                            <p class="text-gray-600">Generate professional receipts automatically with school branding and detailed payment information.</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0 w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 mb-2">Financial Reports</h3>
                            <p class="text-gray-600">Comprehensive financial reporting with income statements, defaulter lists, and collection analysis.</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0 w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 mb-2">Parent Fee Portal</h3>
                            <p class="text-gray-600">Allow parents to view fee status, payment history, and receive automated reminders.</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="bg-gradient-to-br from-yellow-50 to-orange-50 p-8 rounded-2xl">
                <div class="bg-white rounded-xl p-6 shadow-lg">
                    <h4 class="font-semibold text-gray-900 mb-4">Fee Collection Dashboard</h4>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500">Total Collection</span>
                            <span class="font-semibold text-green-600">₹2,45,750</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500">Pending Fees</span>
                            <span class="font-semibold text-red-600">₹45,200</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500">Collection Rate</span>
                            <span class="font-semibold text-blue-600">84.5%</span>
                        </div>
                        <div class="pt-2">
                            <div class="bg-gray-200 rounded-full h-2">
                                <div class="bg-green-500 rounded-full h-2" style="width: 84.5%"></div>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4 pt-2">
                            <div class="text-center">
                                <div class="text-lg font-semibold">127</div>
                                <div class="text-xs text-gray-500">Paid Students</div>
                            </div>
                            <div class="text-center">
                                <div class="text-lg font-semibold">23</div>
                                <div class="text-xs text-gray-500">Pending Students</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="section-padding bg-primary-600 text-white">
    <div class="container-custom text-center">
        <h2 class="text-3xl md:text-4xl font-bold font-heading mb-6">
            Ready to Experience These Features?
        </h2>
        <p class="text-xl text-primary-100 mb-8 max-w-2xl mx-auto">
            See how EduVault Pro can transform your school management with these powerful features and many more.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('contact') }}" class="bg-white text-primary-600 hover:bg-gray-100 font-medium py-3 px-8 rounded-lg transition-colors duration-200">
                Request Demo
            </a>
            <a href="{{ route('pricing') }}" class="border-2 border-white text-white hover:bg-white hover:text-primary-600 font-medium py-3 px-8 rounded-lg transition-colors duration-200">
                View Pricing
            </a>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@endpush
