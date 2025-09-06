<!-- Monthly Comparison Report -->
<div class="space-y-6">
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="p-6 bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg text-white">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-blue-100">Current Year</p>
                    <p class="text-2xl font-bold">{{ $data['current_year'] }}</p>
                    <p class="text-xs text-blue-200">Academic year</p>
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
                    <p class="text-sm font-medium text-green-100">Current Total</p>
                    <p class="text-2xl font-bold">₹{{ number_format($data['current_total'], 2) }}</p>
                    <p class="text-xs text-green-200">This academic year</p>
                </div>
            </div>
        </div>

        <div class="p-6 bg-gradient-to-r from-gray-500 to-gray-600 rounded-lg text-white">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-100">Previous Total</p>
                    <p class="text-2xl font-bold">₹{{ number_format($data['previous_total'], 2) }}</p>
                    <p class="text-xs text-gray-200">{{ $data['previous_year'] }} academic year</p>
                </div>
            </div>
        </div>

        <div class="p-6 bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg text-white">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-purple-100">YoY Growth</p>
                    @php
                        $growth = $data['previous_total'] > 0 
                            ? (($data['current_total'] - $data['previous_total']) / $data['previous_total']) * 100 
                            : 0;
                    @endphp
                    <p class="text-2xl font-bold">
                        @if($growth >= 0)
                            +{{ number_format($growth, 1) }}%
                        @else
                            {{ number_format($growth, 1) }}%
                        @endif
                    </p>
                    <p class="text-xs text-purple-200">Year over year</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Comparison Chart -->
    <div class="bg-white rounded-lg shadow p-6">
        <h4 class="text-lg font-medium text-gray-900 mb-4">Monthly Comparison Chart</h4>
        <div class="space-y-4">
            @foreach($data['comparison'] as $monthData)
                <div class="p-4 bg-gray-50 rounded-lg">
                    <div class="flex items-center justify-between mb-3">
                        <div>
                            <h5 class="font-medium text-gray-900">{{ $monthData['month'] }}</h5>
                            <p class="text-sm text-gray-600">
                                Current: {{ $monthData['current_count'] }} payments | 
                                Previous: {{ $monthData['previous_count'] }} payments
                            </p>
                        </div>
                        <div class="text-right">
                            <div class="text-lg font-bold text-blue-600">
                                ₹{{ number_format($monthData['current_amount'], 2) }}
                            </div>
                            <div class="text-sm text-gray-500">
                                vs ₹{{ number_format($monthData['previous_amount'], 2) }}
                            </div>
                        </div>
                    </div>
                    
                    <!-- Dual Progress bars -->
                    <div class="space-y-2">
                        <!-- Current Year -->
                        <div>
                            <div class="flex justify-between text-xs text-gray-600 mb-1">
                                <span>{{ $data['current_year'] }}</span>
                                <span>₹{{ number_format($monthData['current_amount'], 2) }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                @php
                                    $maxAmount = max($data['current_total'], $data['previous_total']);
                                    $currentPercentage = $maxAmount > 0 ? ($monthData['current_amount'] / $maxAmount) * 100 : 0;
                                @endphp
                                <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-2 rounded-full transition-all duration-500" 
                                     style="width: {{ $currentPercentage }}%">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Previous Year -->
                        <div>
                            <div class="flex justify-between text-xs text-gray-600 mb-1">
                                <span>{{ $data['previous_year'] }}</span>
                                <span>₹{{ number_format($monthData['previous_amount'], 2) }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                @php
                                    $previousPercentage = $maxAmount > 0 ? ($monthData['previous_amount'] / $maxAmount) * 100 : 0;
                                @endphp
                                <div class="bg-gradient-to-r from-gray-400 to-gray-500 h-2 rounded-full transition-all duration-500" 
                                     style="width: {{ $previousPercentage }}%">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Growth Indicator -->
                    <div class="mt-3 flex items-center justify-between">
                        <div class="text-sm">
                            <span class="text-gray-500">Difference:</span>
                            <span class="font-medium {{ $monthData['amount_difference'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $monthData['amount_difference'] >= 0 ? '+' : '' }}₹{{ number_format($monthData['amount_difference'], 2) }}
                            </span>
                        </div>
                        <div class="text-sm">
                            <span class="text-gray-500">Growth:</span>
                            <span class="font-medium {{ $monthData['percentage_change'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $monthData['percentage_change'] >= 0 ? '+' : '' }}{{ number_format($monthData['percentage_change'], 1) }}%
                            </span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Comparison Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b">
            <h4 class="text-lg font-medium text-gray-900">Detailed Monthly Comparison</h4>
            <p class="text-sm text-gray-600">Side-by-side comparison of {{ $data['current_year'] }} vs {{ $data['previous_year'] }}</p>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Month</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $data['current_year'] }} Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $data['previous_year'] }} Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Difference</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Growth %</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payments Comparison</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($data['comparison'] as $monthData)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $monthData['month'] }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-blue-600">₹{{ number_format($monthData['current_amount'], 2) }}</div>
                                <div class="text-xs text-gray-500">{{ $monthData['current_count'] }} payments</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-600">₹{{ number_format($monthData['previous_amount'], 2) }}</div>
                                <div class="text-xs text-gray-500">{{ $monthData['previous_count'] }} payments</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-medium {{ $monthData['amount_difference'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $monthData['amount_difference'] >= 0 ? '+' : '' }}₹{{ number_format($monthData['amount_difference'], 2) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    {{ $monthData['percentage_change'] >= 10 ? 'bg-green-100 text-green-800' : 
                                       ($monthData['percentage_change'] >= 0 ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800') }}">
                                    {{ $monthData['percentage_change'] >= 0 ? '+' : '' }}{{ number_format($monthData['percentage_change'], 1) }}%
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ $monthData['count_difference'] >= 0 ? '+' : '' }}{{ $monthData['count_difference'] }} payments
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $monthData['current_count'] }} vs {{ $monthData['previous_count'] }}
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Year-over-Year Analysis -->
    <div class="bg-white rounded-lg shadow p-6">
        <h4 class="text-lg font-medium text-gray-900 mb-4">Year-over-Year Analysis</h4>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Best Performing Months -->
            <div class="p-4 bg-green-50 rounded-lg border border-green-200">
                <h5 class="font-medium text-green-900 mb-2">Best Growth Months</h5>
                <div class="space-y-2">
                    @foreach(collect($data['comparison'])->sortByDesc('percentage_change')->take(3) as $month)
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-green-800">{{ $month['month'] }}</span>
                            <span class="text-sm font-medium text-green-900">+{{ number_format($month['percentage_change'], 1) }}%</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Declining Months -->
            <div class="p-4 bg-red-50 rounded-lg border border-red-200">
                <h5 class="font-medium text-red-900 mb-2">Declining Months</h5>
                <div class="space-y-2">
                    @foreach(collect($data['comparison'])->sortBy('percentage_change')->take(3) as $month)
                        @if($month['percentage_change'] < 0)
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-red-800">{{ $month['month'] }}</span>
                                <span class="text-sm font-medium text-red-900">{{ number_format($month['percentage_change'], 1) }}%</span>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

            <!-- Key Insights -->
            <div class="p-4 bg-blue-50 rounded-lg border border-blue-200">
                <h5 class="font-medium text-blue-900 mb-2">Key Insights</h5>
                <ul class="text-sm text-blue-800 space-y-1">
                    @php
                        $totalGrowth = $data['previous_total'] > 0 
                            ? (($data['current_total'] - $data['previous_total']) / $data['previous_total']) * 100 
                            : 0;
                        $growthMonths = collect($data['comparison'])->where('percentage_change', '>', 0)->count();
                        $declineMonths = collect($data['comparison'])->where('percentage_change', '<', 0)->count();
                    @endphp
                    <li>• Overall growth: {{ number_format($totalGrowth, 1) }}%</li>
                    <li>• Growth months: {{ $growthMonths }}</li>
                    <li>• Decline months: {{ $declineMonths }}</li>
                    <li>• Performance trend: {{ $totalGrowth >= 0 ? 'Positive' : 'Negative' }}</li>
                </ul>
            </div>
        </div>

        <!-- Strategic Recommendations -->
        <div class="mt-6 p-4 bg-yellow-50 rounded-lg border border-yellow-200">
            <h5 class="font-medium text-yellow-900 mb-2">Strategic Recommendations</h5>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-yellow-800">
                <ul class="space-y-1">
                    <li>• Analyze factors behind high-growth months</li>
                    <li>• Develop action plans for declining periods</li>
                    <li>• Consider seasonal payment incentives</li>
                </ul>
                <ul class="space-y-1">
                    <li>• Review fee collection strategies</li>
                    <li>• Plan resources based on trends</li>
                    <li>• Set realistic targets for next year</li>
                </ul>
            </div>
        </div>
    </div>
</div>
