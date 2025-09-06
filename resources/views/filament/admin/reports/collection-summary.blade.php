<!-- Collection Summary Report -->
<div class="space-y-6">
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="p-6 bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg text-white">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-blue-100">Total Collection</p>
                    <p class="text-2xl font-bold">₹{{ number_format($data['total_collection'], 2) }}</p>
                    <p class="text-xs text-blue-200">{{ $data['period']['from'] }} - {{ $data['period']['to'] }}</p>
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-purple-100">Average Payment</p>
                    <p class="text-2xl font-bold">₹{{ number_format($data['average_payment'], 2) }}</p>
                    <p class="text-xs text-purple-200">Per transaction</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Methods Breakdown -->
    <div class="bg-white rounded-lg shadow p-6">
        <h4 class="text-lg font-medium text-gray-900 mb-4">Payment Methods Breakdown</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach($data['payment_methods'] as $method => $methodData)
                <div class="p-4 bg-gray-50 rounded-lg">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-gray-600">{{ ucfirst(str_replace('_', ' ', $method)) }}</span>
                        <span class="text-lg font-bold text-gray-900">{{ $methodData['count'] }}</span>
                    </div>
                    <div class="text-xl font-bold text-blue-600">₹{{ number_format($methodData['amount'], 2) }}</div>
                    <div class="text-xs text-gray-500">
                        {{ $data['total_collection'] > 0 ? number_format(($methodData['amount'] / $data['total_collection']) * 100, 1) : 0 }}% of total
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Daily Collection Chart -->
    <div class="bg-white rounded-lg shadow p-6">
        <h4 class="text-lg font-medium text-gray-900 mb-4">Daily Collection Trend</h4>
        <div class="space-y-2">
            @foreach($data['daily_collection'] as $date => $dayData)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                    <div>
                        <span class="font-medium text-gray-900">{{ \Carbon\Carbon::parse($date)->format('M d, Y') }}</span>
                        <span class="text-sm text-gray-500 ml-2">({{ $dayData['count'] }} payments)</span>
                    </div>
                    <div class="font-bold text-blue-600">₹{{ number_format($dayData['amount'], 2) }}</div>
                </div>
            @endforeach
        </div>
    </div>
</div>
