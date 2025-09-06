<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Header Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Students</dt>
                            <dd class="text-lg font-medium text-gray-900">
                                {{ $this->getTableQuery()->count() }}
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Class Average</dt>
                            <dd class="text-lg font-medium text-gray-900">
                                @if($this->selectedSubject && $this->selectedClass)
                                    @php
                                        $students = $this->getTableQuery()->get();
                                        $totalPercentage = 0;
                                        $count = 0;
                                        foreach($students as $student) {
                                            $grades = $student->grades()->where('subject_id', $this->selectedSubject->id)->get();
                                            if($grades->isNotEmpty()) {
                                                $totalPercentage += $grades->avg('percentage');
                                                $count++;
                                            }
                                        }
                                        $average = $count > 0 ? round($totalPercentage / $count, 1) : 0;
                                    @endphp
                                    {{ $average }}%
                                @else
                                    -
                                @endif
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-500 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Avg Attendance</dt>
                            <dd class="text-lg font-medium text-gray-900">
                                @if($this->selectedClass)
                                    @php
                                        $students = $this->getTableQuery()->get();
                                        $totalAttendance = 0;
                                        $studentCount = 0;
                                        foreach($students as $student) {
                                            $total = \App\Models\Attendance::where('student_id', $student->id)
                                                ->where('class_id', $this->selectedClass->id)
                                                ->count();
                                            if($total > 0) {
                                                $present = \App\Models\Attendance::where('student_id', $student->id)
                                                    ->where('class_id', $this->selectedClass->id)
                                                    ->where('status', 'present')
                                                    ->count();
                                                $totalAttendance += ($present / $total) * 100;
                                                $studentCount++;
                                            }
                                        }
                                        $avgAttendance = $studentCount > 0 ? round($totalAttendance / $studentCount, 1) : 0;
                                    @endphp
                                    {{ $avgAttendance }}%
                                @else
                                    -
                                @endif
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-purple-500 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Assignments</dt>
                            <dd class="text-lg font-medium text-gray-900">
                                @if($this->selectedSubject && $this->selectedClass)
                                    {{ \App\Models\Assignment::where('class_id', $this->selectedClass->id)
                                        ->where('subject_id', $this->selectedSubject->id)
                                        ->count() }}
                                @else
                                    -
                                @endif
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters Form -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Filter Students</h3>
            </div>
            <div class="p-6">
                {{ $this->form }}
            </div>
        </div>

        <!-- Students Table -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Student Performance</h3>
                @if($this->selectedClass && $this->selectedSubject)
                    <p class="mt-1 text-sm text-gray-600">
                        Showing data for {{ $this->selectedClass->name }} - {{ $this->selectedSubject->name }}
                    </p>
                @endif
            </div>
            <div class="p-6">
                {{ $this->table }}
            </div>
        </div>
    </div>
</x-filament-panels::page>
