<!-- Fee Category Analysis Report -->
<div class="space-y-6">
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="p-6 bg-gradient-to-r from-indigo-500 to-indigo-600 rounded-lg text-white">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-indigo-100">Total Collection</p>
                    <p class="text-2xl font-bold">₹{{ number_format($data['total_amount'], 2) }}</p>
                    <p class="text-xs text-indigo-200">Across all categories</p>
                </div>
            </div>
        </div>

        <div class="p-6 bg-gradient-to-r from-green-500 to-green-600 rounded-lg text-white">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-green-100">Total Payments</p>
                    <p class="text-2xl font-bold">{{ number_format($data['total_payments']) }}</p>
                    <p class="text-xs text-green-200">Payment transactions</p>
                </div>
            </div>
        </div>

        <div class="p-6 bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg text-white">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-purple-100">Categories</p>
                    <p class="text-2xl font-bold">{{ $data['categories']->count() }}</p>
                    <p class="text-xs text-purple-200">Fee categories</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Category Performance Chart -->
    <div class="bg-white rounded-lg shadow p-6">
        <h4 class="text-lg font-medium text-gray-900 mb-4">Fee Category Performance</h4>
        <div class="space-y-4">
            @foreach($data['categories'] as $category)
                <div class="p-4 bg-gray-50 rounded-lg">
                    <div class="flex items-center justify-between mb-3">
                        <div>
                            <h5 class="font-medium text-gray-900">{{ $category['category'] }}</h5>
                            <p class="text-sm text-gray-600">{{ $category['count'] }} payments</p>
                        </div>
                        <div class="text-right">
                            <div class="text-xl font-bold text-indigo-600">₹{{ number_format($category['amount'], 2) }}</div>
                            <div class="text-sm text-gray-500">{{ number_format($category['percentage'], 1) }}% of total</div>
                        </div>
                    </div>
                    
                    <!-- Progress bar -->
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div class="bg-gradient-to-r from-indigo-500 to-indigo-600 h-3 rounded-full transition-all duration-500" 
                             style="width: {{ $category['percentage'] }}%">
                        </div>
                    </div>
                    
                    <!-- Additional metrics -->
                    <div class="mt-3 grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-gray-500">Average Payment:</span>
                            <span class="font-medium text-gray-900 ml-2">
                                ₹{{ $category['count'] > 0 ? number_format($category['amount'] / $category['count'], 2) : 0 }}
                            </span>
                        </div>
                        <div>
                            <span class="text-gray-500">Collection Rate:</span>
                            <span class="font-medium text-gray-900 ml-2">
                                {{ number_format($category['percentage'], 1) }}%
                            </span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Category Comparison Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b">
            <h4 class="text-lg font-medium text-gray-900">Category Comparison Table</h4>
            <p class="text-sm text-gray-600">Detailed breakdown of fee collection by category</p>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payments</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Percentage</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Average</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Performance</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($data['categories'] as $index => $category)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-3 w-3 rounded-full mr-3" 
                                         style="background-color: {{ ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#06B6D4'][$index % 6] }}">
                                    </div>
                                    <div class="text-sm font-medium text-gray-900">{{ $category['category'] }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ number_format($category['count']) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">₹{{ number_format($category['amount'], 2) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    {{ $category['percentage'] >= 25 ? 'bg-green-100 text-green-800' : 
                                       ($category['percentage'] >= 15 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                    {{ number_format($category['percentage'], 1) }}%
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    ₹{{ $category['count'] > 0 ? number_format($category['amount'] / $category['count'], 2) : 0 }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($category['percentage'] >= 25)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Excellent
                                    </span>
                                @elseif($category['percentage'] >= 15)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        Good
                                    </span>
                                @elseif($category['percentage'] >= 5)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                        Fair
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

    <!-- Insights and Recommendations -->
    <div class="bg-white rounded-lg shadow p-6">
        <h4 class="text-lg font-medium text-gray-900 mb-4">Insights & Recommendations</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Top Performing Categories -->
            <div class="p-4 bg-green-50 rounded-lg border border-green-200">
                <h5 class="font-medium text-green-900 mb-2">Top Performing Categories</h5>
                <div class="space-y-2">
                    @foreach($data['categories']->take(3) as $category)
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-green-800">{{ $category['category'] }}</span>
                            <span class="text-sm font-medium text-green-900">{{ number_format($category['percentage'], 1) }}%</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Areas for Improvement -->
            <div class="p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                <h5 class="font-medium text-yellow-900 mb-2">Areas for Improvement</h5>
                <div class="space-y-2">
                    @foreach($data['categories']->reverse()->take(3) as $category)
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-yellow-800">{{ $category['category'] }}</span>
                            <span class="text-sm font-medium text-yellow-900">{{ number_format($category['percentage'], 1) }}%</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Recommendations -->
        <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
            <h5 class="font-medium text-blue-900 mb-2">Recommendations</h5>
            <ul class="text-sm text-blue-800 space-y-1">
                <li>• Focus collection efforts on underperforming categories</li>
                <li>• Consider payment plan options for high-value categories</li>
                <li>• Review fee structure for categories with low collection rates</li>
                <li>• Implement targeted reminders for specific fee categories</li>
            </ul>
        </div>
    </div>
</div>
