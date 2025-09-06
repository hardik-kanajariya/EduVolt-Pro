<!-- Outstanding Analysis Report -->
<div class="space-y-6">
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="p-6 bg-gradient-to-r from-orange-500 to-orange-600 rounded-lg text-white">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-orange-100">Total Outstanding</p>
                    <p class="text-2xl font-bold">₹{{ number_format($data['summary']['total_outstanding'], 2) }}</p>
                    <p class="text-xs text-orange-200">Across all categories</p>
                </div>
            </div>
        </div>

        <div class="p-6 bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg text-white">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-blue-100">Affected Students</p>
                    <p class="text-2xl font-bold">{{ number_format($data['summary']['total_students']) }}</p>
                    <p class="text-xs text-blue-200">With pending payments</p>
                </div>
            </div>
        </div>

        <div class="p-6 bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg text-white">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-purple-100">Pending Installments</p>
                    <p class="text-2xl font-bold">{{ number_format($data['summary']['total_installments']) }}</p>
                    <p class="text-xs text-purple-200">Outstanding payments</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Class-wise Outstanding Analysis -->
    <div class="bg-white rounded-lg shadow p-6">
        <h4 class="text-lg font-medium text-gray-900 mb-4">Class-wise Outstanding Analysis</h4>
        <div class="space-y-4">
            @foreach($data['class_wise'] as $classData)
                <div class="p-4 bg-gray-50 rounded-lg">
                    <div class="flex items-center justify-between mb-2">
                        <div>
                            <h5 class="font-medium text-gray-900">Class {{ $classData['class'] }}</h5>
                            <p class="text-sm text-gray-600">{{ $classData['students'] }} students affected</p>
                        </div>
                        <div class="text-right">
                            <div class="text-xl font-bold text-orange-600">₹{{ number_format($classData['total_outstanding'], 2) }}</div>
                            <div class="text-sm text-gray-500">{{ $classData['installments'] }} installments</div>
                        </div>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-orange-600 h-2 rounded-full" 
                             style="width: {{ $data['summary']['total_outstanding'] > 0 ? ($classData['total_outstanding'] / $data['summary']['total_outstanding']) * 100 : 0 }}%">
                        </div>
                    </div>
                    <div class="text-xs text-gray-500 mt-1">
                        {{ $data['summary']['total_outstanding'] > 0 ? number_format(($classData['total_outstanding'] / $data['summary']['total_outstanding']) * 100, 1) : 0 }}% of total outstanding
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Category-wise Outstanding Analysis -->
    <div class="bg-white rounded-lg shadow p-6">
        <h4 class="text-lg font-medium text-gray-900 mb-4">Fee Category-wise Outstanding Analysis</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($data['category_wise'] as $categoryData)
                <div class="p-4 bg-gradient-to-r from-red-50 to-red-100 rounded-lg border border-red-200">
                    <div class="flex items-center justify-between mb-3">
                        <div>
                            <h5 class="font-medium text-gray-900">{{ $categoryData['category'] }}</h5>
                            <p class="text-sm text-gray-600">{{ $categoryData['students'] }} students</p>
                        </div>
                        <div class="text-right">
                            <div class="text-lg font-bold text-red-600">₹{{ number_format($categoryData['total_outstanding'], 2) }}</div>
                            <div class="text-xs text-gray-500">{{ $categoryData['installments'] }} installments</div>
                        </div>
                    </div>
                    
                    <!-- Progress bar -->
                    <div class="w-full bg-red-200 rounded-full h-2">
                        <div class="bg-red-600 h-2 rounded-full" 
                             style="width: {{ $data['summary']['total_outstanding'] > 0 ? ($categoryData['total_outstanding'] / $data['summary']['total_outstanding']) * 100 : 0 }}%">
                        </div>
                    </div>
                    <div class="text-xs text-gray-600 mt-1">
                        {{ $data['summary']['total_outstanding'] > 0 ? number_format(($categoryData['total_outstanding'] / $data['summary']['total_outstanding']) * 100, 1) : 0 }}% of total outstanding
                    </div>
                    
                    <!-- Average per student -->
                    <div class="mt-2 pt-2 border-t border-red-200">
                        <div class="text-xs text-gray-600">
                            Average per student: 
                            <span class="font-medium text-red-700">
                                ₹{{ $categoryData['students'] > 0 ? number_format($categoryData['total_outstanding'] / $categoryData['students'], 2) : 0 }}
                            </span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-lg shadow p-6">
        <h4 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h4>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <button class="p-4 bg-blue-50 hover:bg-blue-100 rounded-lg border border-blue-200 transition-colors duration-200" onclick="generateReminderList()">
                <div class="flex items-center">
                    <svg class="h-6 w-6 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4.828 2.828A4 4 0 015.636 2h8.728a2 2 0 011.414.586l2.828 2.828A2 2 0 0119 6.828V11a4 4 0 01-.172.828L17 13v-2a3 3 0 00-3-3H4.828z"></path>
                    </svg>
                    <div class="text-left">
                        <div class="font-medium text-blue-900">Generate Reminder List</div>
                        <div class="text-sm text-blue-700">Create list for fee reminders</div>
                    </div>
                </div>
            </button>

            <button class="p-4 bg-green-50 hover:bg-green-100 rounded-lg border border-green-200 transition-colors duration-200" onclick="exportOutstandingReport()">
                <div class="flex items-center">
                    <svg class="h-6 w-6 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <div class="text-left">
                        <div class="font-medium text-green-900">Export Report</div>
                        <div class="text-sm text-green-700">Download Excel/PDF report</div>
                    </div>
                </div>
            </button>

            <button class="p-4 bg-purple-50 hover:bg-purple-100 rounded-lg border border-purple-200 transition-colors duration-200" onclick="scheduleFollowUp()">
                <div class="flex items-center">
                    <svg class="h-6 w-6 text-purple-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <div class="text-left">
                        <div class="font-medium text-purple-900">Schedule Follow-up</div>
                        <div class="text-sm text-purple-700">Set automatic reminders</div>
                    </div>
                </div>
            </button>
        </div>
    </div>
</div>

<script>
function generateReminderList() {
    alert('Reminder list generation feature will be implemented');
}

function exportOutstandingReport() {
    alert('Export functionality will be implemented');
}

function scheduleFollowUp() {
    alert('Follow-up scheduling feature will be implemented');
}
</script>
