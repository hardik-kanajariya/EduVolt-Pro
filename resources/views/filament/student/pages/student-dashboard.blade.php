<x-filament-panels::page>
    @php
        $student = $this->getStudent();
        $recentGrades = $this->getRecentGrades();
        $attendanceStats = $this->getAttendanceStats();
        $upcomingAssignments = $this->getUpcomingAssignments();
        $pendingAssignments = $this->getPendingAssignments();
        $overallPerformance = $this->getOverallPerformance();
        $subjectPerformance = $this->getSubjectPerformance();
        $pendingFees = $this->getPendingFees();
        $academicCalendar = $this->getAcademicCalendar();
    @endphp

    <div class="space-y-6">
        <!-- Welcome Section -->
        <div class="bg-gradient-to-r from-indigo-500 to-indigo-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold mb-2">Welcome back, {{ Auth::user()->name }}!</h1>
                    @if($student)
                        <p class="text-indigo-100">
                            {{ $student->schoolClass->name ?? 'No Class Assigned' }} • 
                            Admission No: {{ $student->admission_number }}
                        </p>
                    @endif
                </div>
                <div class="text-right">
                    <div class="text-3xl font-bold">{{ $overallPerformance['grade'] }}</div>
                    <div class="text-indigo-100 text-sm">Current Grade</div>
                </div>
            </div>
        </div>

        <!-- Performance Overview -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Overall Performance -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-green-500 rounded-md flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Academic Performance</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $overallPerformance['performance_level'] }}</p>
                        <p class="text-sm text-gray-500">{{ $overallPerformance['average'] }}% Average</p>
                    </div>
                </div>
            </div>

            <!-- Attendance -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-blue-500 rounded-md flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Attendance</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $attendanceStats['percentage'] }}%</p>
                        <p class="text-sm text-gray-500">{{ $attendanceStats['present'] }}/{{ $attendanceStats['total'] }} days</p>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $attendanceStats['percentage'] }}%"></div>
                    </div>
                </div>
            </div>

            <!-- Pending Assignments -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-yellow-500 rounded-md flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Pending Tasks</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $pendingAssignments->count() }}</p>
                        <p class="text-sm text-gray-500">Assignments due</p>
                    </div>
                </div>
            </div>

            <!-- Pending Fees -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-red-500 rounded-md flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Pending Fees</p>
                        <p class="text-2xl font-semibold text-gray-900">₹{{ number_format($pendingFees->sum('balance_amount'), 2) }}</p>
                        <p class="text-sm text-gray-500">{{ $pendingFees->count() }} installments</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column - Academic Performance -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Recent Grades -->
                @if($recentGrades->isNotEmpty())
                    <div class="bg-white rounded-lg shadow">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Recent Grades</h3>
                        </div>
                        <div class="p-6">
                            <div class="space-y-3">
                                @foreach($recentGrades as $grade)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-10 h-10 bg-indigo-500 rounded-full flex items-center justify-center">
                                                <span class="text-white font-semibold">{{ $grade->grade }}</span>
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-900">{{ $grade->subject->name }}</p>
                                                <p class="text-sm text-gray-500">{{ $grade->exam_type }} • {{ $grade->exam_date->format('d M Y') }}</p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-semibold text-gray-900">{{ $grade->marks_obtained }}/{{ $grade->total_marks }}</p>
                                            <p class="text-sm text-gray-500">{{ round($grade->percentage, 1) }}%</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Subject Performance -->
                @if($subjectPerformance->isNotEmpty())
                    <div class="bg-white rounded-lg shadow">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Subject Performance</h3>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($subjectPerformance as $subject)
                                    <div class="p-4 border border-gray-200 rounded-lg">
                                        <div class="flex items-center justify-between mb-2">
                                            <h4 class="font-medium text-gray-900">{{ $subject['subject']->name }}</h4>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                @if($subject['average'] >= 90) bg-green-100 text-green-800
                                                @elseif($subject['average'] >= 75) bg-blue-100 text-blue-800
                                                @elseif($subject['average'] >= 60) bg-yellow-100 text-yellow-800
                                                @else bg-red-100 text-red-800
                                                @endif">
                                                {{ $subject['grade'] }}
                                            </span>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm text-gray-500">Average: {{ $subject['average'] }}%</span>
                                            <span class="text-sm text-gray-500">{{ $subject['total_exams'] }} exams</span>
                                        </div>
                                        <div class="mt-2">
                                            <div class="w-full bg-gray-200 rounded-full h-2">
                                                <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ $subject['average'] }}%"></div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Right Column - Tasks & Calendar -->
            <div class="space-y-6">
                <!-- Upcoming Assignments -->
                @if($upcomingAssignments->isNotEmpty())
                    <div class="bg-white rounded-lg shadow">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Upcoming Assignments</h3>
                        </div>
                        <div class="p-6">
                            <div class="space-y-3">
                                @foreach($upcomingAssignments as $assignment)
                                    <div class="flex items-start space-x-3 p-3 bg-yellow-50 rounded-lg">
                                        <div class="flex-shrink-0 w-2 h-2 bg-yellow-400 rounded-full mt-2"></div>
                                        <div class="flex-1 min-w-0">
                                            <p class="font-medium text-gray-900 truncate">{{ $assignment->title }}</p>
                                            <p class="text-sm text-gray-500">{{ $assignment->subject->name }}</p>
                                            <p class="text-sm text-gray-500">Due: {{ $assignment->due_date->format('d M Y') }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Pending Fees -->
                @if($pendingFees->isNotEmpty())
                    <div class="bg-white rounded-lg shadow">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Pending Fees</h3>
                        </div>
                        <div class="p-6">
                            <div class="space-y-3">
                                @foreach($pendingFees as $fee)
                                    <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg">
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $fee->installment_name }}</p>
                                            <p class="text-sm text-gray-500">Due: {{ $fee->due_date->format('d M Y') }}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-semibold text-red-600">₹{{ number_format($fee->balance_amount, 2) }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Academic Calendar -->
                @if($academicCalendar->isNotEmpty())
                    <div class="bg-white rounded-lg shadow">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Upcoming Events</h3>
                        </div>
                        <div class="p-6">
                            <div class="space-y-3">
                                @foreach($academicCalendar->take(5) as $event)
                                    <div class="flex items-start space-x-3">
                                        <div class="flex-shrink-0 w-3 h-3 rounded-full mt-1
                                            @if($event['color'] === 'blue') bg-blue-400
                                            @elseif($event['color'] === 'red') bg-red-400
                                            @elseif($event['color'] === 'green') bg-green-400
                                            @else bg-gray-400
                                            @endif">
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="font-medium text-gray-900">{{ $event['title'] }}</p>
                                            <p class="text-sm text-gray-500">{{ $event['subject'] }} • {{ $event['date']->format('d M Y') }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-filament-panels::page>
