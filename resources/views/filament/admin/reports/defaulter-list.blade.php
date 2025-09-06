<!-- Defaulter List Report -->
<div class="space-y-6">
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="p-6 bg-gradient-to-r from-red-500 to-red-600 rounded-lg text-white">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-red-100">Total Defaulters</p>
                    <p class="text-2xl font-bold">{{ $data['summary']['total_defaulters'] }}</p>
                    <p class="text-xs text-red-200">Students with overdue fees</p>
                </div>
            </div>
        </div>

        <div class="p-6 bg-gradient-to-r from-orange-500 to-orange-600 rounded-lg text-white">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-orange-100">Total Overdue</p>
                    <p class="text-2xl font-bold">₹{{ number_format($data['summary']['total_overdue_amount'], 2) }}</p>
                    <p class="text-xs text-orange-200">Outstanding amount</p>
                </div>
            </div>
        </div>

        <div class="p-6 bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-lg text-white">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-yellow-100">Average Overdue</p>
                    <p class="text-2xl font-bold">₹{{ number_format($data['summary']['average_overdue'], 2) }}</p>
                    <p class="text-xs text-yellow-200">Per defaulter</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Defaulters List -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b">
            <h4 class="text-lg font-medium text-gray-900">Defaulter Students List</h4>
            <p class="text-sm text-gray-600">Students with overdue fee payments ordered by amount</p>
        </div>
        
        @if($data['defaulters']->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student Details</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Class</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Overdue Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Overdue Count</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Days Overdue</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($data['defaulters'] as $defaulter)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $defaulter['student_name'] }}</div>
                                        <div class="text-sm text-gray-500">ID: {{ $defaulter['student_id'] }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $defaulter['class'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-red-600">₹{{ number_format($defaulter['total_overdue'], 2) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        {{ $defaulter['overdue_count'] }} installments
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $defaulter['days_overdue'] }} days</div>
                                    <div class="text-xs text-gray-500">Since {{ \Carbon\Carbon::parse($defaulter['oldest_due_date'])->format('M d, Y') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button class="text-indigo-600 hover:text-indigo-900 mr-3" onclick="viewDefaulterDetails({{ $defaulter['student_id'] }})">
                                        View Details
                                    </button>
                                    <button class="text-green-600 hover:text-green-900" onclick="sendReminder({{ $defaulter['student_id'] }})">
                                        Send Reminder
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="p-8 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No Defaulters Found</h3>
                <p class="mt-1 text-sm text-gray-500">All students are up to date with their fee payments.</p>
            </div>
        @endif
    </div>
</div>

<script>
function viewDefaulterDetails(studentId) {
    // Implement modal or navigation to student details
    alert('View defaulter details for student ID: ' + studentId);
}

function sendReminder(studentId) {
    // Implement reminder functionality
    if (confirm('Send fee reminder to this student?')) {
        // Add AJAX call to send reminder
        alert('Reminder sent successfully!');
    }
}
</script>
