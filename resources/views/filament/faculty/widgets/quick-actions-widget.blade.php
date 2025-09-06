<div class="fi-wi-stats-overview">
    <div class="grid gap-4">
        <!-- Today's Classes Section -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                <x-heroicon-m-calendar-days class="inline w-5 h-5 mr-2" />
                Today's Classes
            </h3>

            @if($this->getTodayClasses()->count() > 0)
                <div class="space-y-3">
                    @foreach($this->getTodayClasses() as $schedule)
                        <div class="flex justify-between items-center p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                            <div>
                                <span class="font-medium text-blue-900 dark:text-blue-100">
                                    {{ $schedule->schoolClass->name ?? 'N/A' }}
                                </span>
                                <span class="text-sm text-blue-700 dark:text-blue-300 ml-2">
                                    Period {{ $schedule->period_number ?? 'N/A' }}
                                </span>
                            </div>
                            <div class="text-sm text-blue-600 dark:text-blue-400">
                                {{ $schedule->start_time ?? 'N/A' }} - {{ $schedule->end_time ?? 'N/A' }}
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 dark:text-gray-400">No classes scheduled for today.</p>
            @endif
        </div>

        <!-- Quick Actions Section -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                <x-heroicon-m-bolt class="inline w-5 h-5 mr-2" />
                Quick Actions
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Mark Attendance -->
                <a href="{{ route('filament.faculty.pages.bulk-attendance') }}"
                    class="flex flex-col items-center p-4 bg-green-50 dark:bg-green-900/20 rounded-lg hover:bg-green-100 dark:hover:bg-green-900/30 transition-colors">
                    <x-heroicon-o-user-group class="w-8 h-8 text-green-600 dark:text-green-400 mb-2" />
                    <span class="text-sm font-medium text-green-900 dark:text-green-100">Mark Attendance</span>
                    @if($this->getUnmarkedAttendance()->count() > 0)
                        <span class="text-xs text-red-600 dark:text-red-400 mt-1">
                            {{ $this->getUnmarkedAttendance()->count() }} classes pending
                        </span>
                    @endif
                </a>

                <!-- Create Assignment -->
                <a href="{{ route('filament.faculty.resources.assignments.create') }}"
                    class="flex flex-col items-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors">
                    <x-heroicon-o-document-plus class="w-8 h-8 text-blue-600 dark:text-blue-400 mb-2" />
                    <span class="text-sm font-medium text-blue-900 dark:text-blue-100">Create Assignment</span>
                    <span class="text-xs text-blue-600 dark:text-blue-400 mt-1">
                        {{ $this->getPendingAssignments() }} active assignments
                    </span>
                </a>

                <!-- View Schedule -->
                <a href="{{ route('filament.faculty.resources.timetables.index') }}"
                    class="flex flex-col items-center p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg hover:bg-purple-100 dark:hover:bg-purple-900/30 transition-colors">
                    <x-heroicon-o-clock class="w-8 h-8 text-purple-600 dark:text-purple-400 mb-2" />
                    <span class="text-sm font-medium text-purple-900 dark:text-purple-100">View Schedule</span>
                    <span class="text-xs text-purple-600 dark:text-purple-400 mt-1">Full timetable</span>
                </a>
            </div>
        </div>

        <!-- Attendance Alerts -->
        @if($this->getUnmarkedAttendance()->count() > 0)
            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                <div class="flex items-start">
                    <x-heroicon-m-exclamation-triangle class="w-5 h-5 text-red-600 dark:text-red-400 mt-0.5 mr-3" />
                    <div>
                        <h4 class="text-sm font-medium text-red-900 dark:text-red-100">
                            Attendance Pending
                        </h4>
                        <p class="text-sm text-red-700 dark:text-red-300 mt-1">
                            Please mark attendance for the following classes:
                        </p>
                        <ul class="text-sm text-red-700 dark:text-red-300 mt-2 list-disc list-inside">
                            @foreach($this->getUnmarkedAttendance() as $class)
                                <li>{{ $class->name ?? 'Unknown Class' }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>