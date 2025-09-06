<!-- Payment Trends Report -->
<div class="space-y-6">
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="p-6 bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg text-white">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-blue-100">Academic Year</p>
                    <p class="text-2xl font-bold">{{ $data['academic_year'] }}</p>
                    <p class="text-xs text-blue-200">Payment analysis period</p>
                </div>
            </div>
        </div>

        <div class="p-6 bg-gradient-to-r from-green-500 to-green-600 rounded-lg text-white">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-green-100">Total Collection</p>
                    <p class="text-2xl font-bold">₹{{ number_format($data['total_amount'], 2) }}</p>
                    <p class="text-xs text-green-200">For the academic year</p>
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
                    <p class="text-sm font-medium text-purple-100">Total Payments</p>
                    <p class="text-2xl font-bold">{{ number_format($data['total_payments']) }}</p>
                    <p class="text-xs text-purple-200">Payment transactions</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Trends Chart -->
    <div class="bg-white rounded-lg shadow p-6">
        <h4 class="text-lg font-medium text-gray-900 mb-4">Monthly Payment Trends</h4>
        <div class="space-y-4">
            @foreach($data['monthly_trends'] as $monthData)
                <div class="p-4 bg-gray-50 rounded-lg">
                    <div class="flex items-center justify-between mb-3">
                        <div>
                            <h5 class="font-medium text-gray-900">{{ $monthData['month'] }}</h5>
                            <p class="text-sm text-gray-600">{{ $monthData['count'] }} payments</p>
                        </div>
                        <div class="text-right">
                            <div class="text-xl font-bold text-blue-600">₹{{ number_format($monthData['amount'], 2) }}</div>
                            <div class="text-sm text-gray-500">Avg: ₹{{ number_format($monthData['average'], 2) }}</div>
                        </div>
                    </div>
                    
                    <!-- Progress bar -->
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        @php
                            $maxAmount = $data['monthly_trends']->max('amount');
                            $percentage = $maxAmount > 0 ? ($monthData['amount'] / $maxAmount) * 100 : 0;
                        @endphp
                        <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-3 rounded-full transition-all duration-500" 
                             style="width: {{ $percentage }}%">
                        </div>
                    </div>
                    
                    <!-- Additional metrics -->
                    <div class="mt-3 grid grid-cols-3 gap-4 text-sm">
                        <div>
                            <span class="text-gray-500">Collection:</span>
                            <span class="font-medium text-gray-900 ml-2">
                                {{ $data['total_amount'] > 0 ? number_format(($monthData['amount'] / $data['total_amount']) * 100, 1) : 0 }}%
                            </span>
                        </div>
                        <div>
                            <span class="text-gray-500">Payments:</span>
                            <span class="font-medium text-gray-900 ml-2">
                                {{ $data['total_payments'] > 0 ? number_format(($monthData['count'] / $data['total_payments']) * 100, 1) : 0 }}%
                            </span>
                        </div>
                        <div>
                            <span class="text-gray-500">Growth:</span>
                            <span class="font-medium text-gray-900 ml-2">
                                @php
                                    $prevMonth = $data['monthly_trends']->get($loop->index - 1);
                                    $growth = $prevMonth && $prevMonth['amount'] > 0 
                                        ? (($monthData['amount'] - $prevMonth['amount']) / $prevMonth['amount']) * 100 
                                        : 0;
                                @endphp
                                @if($growth > 0)
                                    <span class="text-green-600">+{{ number_format($growth, 1) }}%</span>
                                @elseif($growth < 0)
                                    <span class="text-red-600">{{ number_format($growth, 1) }}%</span>
                                @else
                                    <span class="text-gray-500">-</span>
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Trends Analysis Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b">
            <h4 class="text-lg font-medium text-gray-900">Monthly Performance Table</h4>
            <p class="text-sm text-gray-600">Detailed monthly breakdown with growth metrics</p>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Month</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payments</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Average</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Growth</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Performance</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($data['monthly_trends'] as $monthData)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $monthData['month'] }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">₹{{ number_format($monthData['amount'], 2) }}</div>
                                <div class="text-xs text-gray-500">
                                    {{ $data['total_amount'] > 0 ? number_format(($monthData['amount'] / $data['total_amount']) * 100, 1) : 0 }}% of total
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ number_format($monthData['count']) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">₹{{ number_format($monthData['average'], 2) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $prevMonth = $data['monthly_trends']->get($loop->index - 1);
                                    $growth = $prevMonth && $prevMonth['amount'] > 0 
                                        ? (($monthData['amount'] - $prevMonth['amount']) / $prevMonth['amount']) * 100 
                                        : 0;
                                @endphp
                                @if($growth > 0)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        +{{ number_format($growth, 1) }}%
                                    </span>
                                @elseif($growth < 0)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        {{ number_format($growth, 1) }}%
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        -
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $percentage = $data['total_amount'] > 0 ? ($monthData['amount'] / $data['total_amount']) * 100 : 0;
                                @endphp
                                @if($percentage >= 15)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Excellent
                                    </span>
                                @elseif($percentage >= 10)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        Good
                                    </span>
                                @elseif($percentage >= 5)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        Average
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Low
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Seasonal Insights -->
    <div class="bg-white rounded-lg shadow p-6">
        <h4 class="text-lg font-medium text-gray-900 mb-4">Seasonal Insights</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Peak Collection Months -->
            <div class="p-4 bg-green-50 rounded-lg border border-green-200">
                <h5 class="font-medium text-green-900 mb-2">Peak Collection Months</h5>
                <div class="space-y-2">
                    @foreach($data['monthly_trends']->sortByDesc('amount')->take(3) as $month)
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-green-800">{{ $month['month'] }}</span>
                            <span class="text-sm font-medium text-green-900">₹{{ number_format($month['amount'], 2) }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Low Collection Months -->
            <div class="p-4 bg-red-50 rounded-lg border border-red-200">
                <h5 class="font-medium text-red-900 mb-2">Low Collection Months</h5>
                <div class="space-y-2">
                    @foreach($data['monthly_trends']->sortBy('amount')->take(3) as $month)
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-red-800">{{ $month['month'] }}</span>
                            <span class="text-sm font-medium text-red-900">₹{{ number_format($month['amount'], 2) }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Recommendations -->
        <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
            <h5 class="font-medium text-blue-900 mb-2">Strategic Recommendations</h5>
            <ul class="text-sm text-blue-800 space-y-1">
                <li>• Plan collection drives during traditionally low months</li>
                <li>• Leverage peak months for advance collection strategies</li>
                <li>• Consider seasonal discounts to encourage early payments</li>
                <li>• Analyze correlation between collection patterns and academic calendar</li>
                <li>• Implement targeted reminder campaigns before low collection periods</li>
            </ul>
        </div>
    </div>
</div>
