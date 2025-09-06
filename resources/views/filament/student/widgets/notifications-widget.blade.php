<div class="space-y-4">
    <!-- Overdue Assignments Alert -->
    @if($this->getOverdueAssignments()->count() > 0)
        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
            <div class="flex items-start">
                <x-heroicon-m-exclamation-triangle class="w-5 h-5 text-red-600 dark:text-red-400 mt-0.5 mr-3" />
                <div class="flex-1">
                    <h4 class="text-sm font-medium text-red-900 dark:text-red-100">
                        Overdue Assignments ({{ $this->getOverdueAssignments()->count() }})
                    </h4>
                    <div class="mt-2 space-y-1">
                        @foreach($this->getOverdueAssignments() as $assignment)
                            <div class="text-sm text-red-700 dark:text-red-300">
                                <span class="font-medium">{{ $assignment->title }}</span>
                                <span class="text-xs ml-2">({{ $assignment->subject->name ?? 'N/A' }})</span>
                                <span class="text-xs ml-2">Due: {{ $assignment->due_date->format('M d') }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Upcoming Exams -->
    @if($this->getUpcomingExams()->count() > 0)
        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
            <div class="flex items-start">
                <x-heroicon-m-academic-cap class="w-5 h-5 text-yellow-600 dark:text-yellow-400 mt-0.5 mr-3" />
                <div class="flex-1">
                    <h4 class="text-sm font-medium text-yellow-900 dark:text-yellow-100">
                        Upcoming Exams ({{ $this->getUpcomingExams()->count() }})
                    </h4>
                    <div class="mt-2 space-y-1">
                        @foreach($this->getUpcomingExams() as $exam)
                            <div class="text-sm text-yellow-700 dark:text-yellow-300">
                                <span class="font-medium">{{ $exam->name }}</span>
                                <span class="text-xs ml-2">{{ $exam->start_date->format('M d, Y') }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Pending Fees -->
    @if($this->getPendingFees()->count() > 0)
        <div class="bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800 rounded-lg p-4">
            <div class="flex items-start">
                <x-heroicon-m-currency-rupee class="w-5 h-5 text-orange-600 dark:text-orange-400 mt-0.5 mr-3" />
                <div class="flex-1">
                    <h4 class="text-sm font-medium text-orange-900 dark:text-orange-100">
                        Pending Fees ({{ $this->getPendingFees()->count() }} installments)
                    </h4>
                    <div class="mt-2 space-y-1">
                        @foreach($this->getPendingFees() as $fee)
                            <div class="text-sm text-orange-700 dark:text-orange-300">
                                <span class="font-medium">₹{{ number_format($fee->amount) }}</span>
                                <span
                                    class="text-xs ml-2">{{ $fee->studentFeeAssignment->feeStructure->feeCategory->name ?? 'Fee' }}</span>
                                <span class="text-xs ml-2">Due: {{ $fee->due_date->format('M d') }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Recent Grades -->
    @if($this->getRecentGrades()->count() > 0)
        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
            <div class="flex items-start">
                <x-heroicon-m-star class="w-5 h-5 text-green-600 dark:text-green-400 mt-0.5 mr-3" />
                <div class="flex-1">
                    <h4 class="text-sm font-medium text-green-900 dark:text-green-100">
                        Recent Grades ({{ $this->getRecentGrades()->count() }})
                    </h4>
                    <div class="mt-2 space-y-1">
                        @foreach($this->getRecentGrades() as $grade)
                            <div class="text-sm text-green-700 dark:text-green-300">
                                <span class="font-medium">{{ $grade->subject->name ?? 'N/A' }}</span>
                                <span class="text-xs ml-2">{{ $grade->marks_obtained }}/{{ $grade->total_marks }}</span>
                                <span class="text-xs ml-2 font-medium">{{ $grade->grade }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- All Clear Message -->
    @if(
            $this->getOverdueAssignments()->count() == 0 &&
            $this->getUpcomingExams()->count() == 0 &&
            $this->getPendingFees()->count() == 0 &&
            $this->getRecentGrades()->count() == 0
        )
        <div class="bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6 text-center">
            <x-heroicon-o-check-circle class="w-12 h-12 text-gray-400 dark:text-gray-500 mx-auto mb-3" />
            <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">
                All Caught Up!
            </h4>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                No pending assignments, exams, or fees. Keep up the great work!
            </p>
        </div>
    @endif
</div>