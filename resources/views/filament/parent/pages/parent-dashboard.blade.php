<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Welcome Section -->
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 text-white">
            <h1 class="text-2xl font-bold mb-2">Welcome back, {{ Auth::user()->name }}!</h1>
            <p class="text-blue-100">Here's an overview of your children's academic progress</p>
        </div>

        <!-- Summary Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-blue-500 rounded-md flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Children</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $this->getChildren()->count() }}</p>
                    </div>
                </div>
            </div>

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
                        <p class="text-2xl font-semibold text-gray-900">₹{{ number_format($this->getTotalPendingFees(), 2) }}</p>
                    </div>
                </div>
            </div>

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
                        <p class="text-sm font-medium text-gray-600">Upcoming Assignments</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $this->getTotalUpcomingAssignments() }}</p>
                    </div>
                </div>
            </div>

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
                        <p class="text-sm font-medium text-gray-600">Recent Payments</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $this->getRecentPayments()->count() }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Children Performance Overview -->
        <div class="space-y-6">
            @foreach($this->getChildrenStats() as $childId => $stats)
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-indigo-500 rounded-full flex items-center justify-center">
                                    <span class="text-white font-semibold text-lg">
                                        {{ substr($stats['child']->user->name, 0, 1) }}
                                    </span>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $stats['child']->user->name }}</h3>
                                    <p class="text-sm text-gray-600">
                                        {{ $stats['child']->schoolClass->name ?? 'No Class' }} • 
                                        {{ $stats['child']->admission_number }}
                                    </p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                    @if($stats['overall_performance']['performance_level'] === 'Excellent') bg-green-100 text-green-800
                                    @elseif($stats['overall_performance']['performance_level'] === 'Good') bg-blue-100 text-blue-800
                                    @elseif($stats['overall_performance']['performance_level'] === 'Satisfactory') bg-yellow-100 text-yellow-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    {{ $stats['overall_performance']['performance_level'] }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                            <!-- Academic Performance -->
                            <div class="text-center">
                                <h4 class="text-sm font-medium text-gray-600 mb-2">Academic Performance</h4>
                                <div class="text-3xl font-bold text-gray-900 mb-1">
                                    {{ $stats['overall_performance']['grade'] }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ $stats['overall_performance']['average'] }}% Average
                                </div>
                                <div class="mt-2">
                                    @if($stats['overall_performance']['trend'] === 'improving')
                                        <span class="inline-flex items-center text-green-600 text-sm">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                            </svg>
                                            Improving
                                        </span>
                                    @elseif($stats['overall_performance']['trend'] === 'declining')
                                        <span class="inline-flex items-center text-red-600 text-sm">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path>
                                            </svg>
                                            Declining
                                        </span>
                                    @else
                                        <span class="inline-flex items-center text-gray-600 text-sm">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                            </svg>
                                            Stable
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Attendance -->
                            <div class="text-center">
                                <h4 class="text-sm font-medium text-gray-600 mb-2">Attendance (This Month)</h4>
                                <div class="text-3xl font-bold text-gray-900 mb-1">
                                    {{ $stats['attendance_percentage'] }}%
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $stats['attendance_percentage'] }}%"></div>
                                </div>
                            </div>

                            <!-- Pending Fees -->
                            <div class="text-center">
                                <h4 class="text-sm font-medium text-gray-600 mb-2">Pending Fees</h4>
                                <div class="text-2xl font-bold text-gray-900 mb-1">
                                    ₹{{ number_format($stats['pending_fees']->sum('balance_amount'), 2) }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ $stats['pending_fees']->count() }} installments
                                </div>
                            </div>

                            <!-- Assignments -->
                            <div class="text-center">
                                <h4 class="text-sm font-medium text-gray-600 mb-2">Upcoming Assignments</h4>
                                <div class="text-3xl font-bold text-gray-900 mb-1">
                                    {{ $stats['upcoming_assignments']->count() }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    Due this week
                                </div>
                            </div>
                        </div>

                        <!-- Recent Grades -->
                        @if($stats['recent_grades']->isNotEmpty())
                            <div class="mt-6 border-t border-gray-200 pt-6">
                                <h4 class="text-sm font-medium text-gray-600 mb-3">Recent Grades</h4>
                                <div class="flex space-x-2 overflow-x-auto">
                                    @foreach($stats['recent_grades'] as $grade)
                                        <div class="flex-shrink-0 bg-gray-50 rounded-lg p-3 min-w-[120px]">
                                            <div class="text-xs text-gray-500">{{ $grade->subject->name }}</div>
                                            <div class="text-lg font-bold text-gray-900">{{ $grade->grade }}</div>
                                            <div class="text-xs text-gray-500">{{ $grade->percentage }}%</div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Recent Payments -->
        @if($this->getRecentPayments()->isNotEmpty())
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Recent Payments</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Method</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Receipt</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($this->getRecentPayments() as $payment)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $payment->feeInstallment->studentFeeAssignment->student->user->name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $payment->formatted_net_amount }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $payment->payment_date->format('d M Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                            {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $payment->receipt_number }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</x-filament-panels::page>
