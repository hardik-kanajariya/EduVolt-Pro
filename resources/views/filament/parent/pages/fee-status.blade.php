<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Child Selection -->
        @if($this->getChildren()->count() > 1)
            <div class="p-4 bg-white rounded-lg shadow">
                <h3 class="text-lg font-medium text-gray-900 mb-3">Select Child</h3>
                <div class="flex flex-wrap gap-2">
                    @foreach($this->getChildren() as $child)
                        <button
                            wire:click="selectChild({{ $child->id }})"
                            class="px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200
                                {{ $selectedChild?->id === $child->id 
                                    ? 'bg-primary-600 text-white' 
                                    : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
                        >
                            {{ $child->name }}
                        </button>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Fee Statistics -->
        @if($selectedChild)
            <div class="p-4 bg-white rounded-lg shadow">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Fee Summary for {{ $selectedChild->name }}</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Total Fee -->
                    <div class="p-4 bg-blue-50 rounded-lg">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-blue-900">Total Fee</p>
                                <p class="text-2xl font-bold text-blue-600">₹{{ number_format($feeStatistics['total_fee'] ?? 0, 2) }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Paid Amount -->
                    <div class="p-4 bg-green-50 rounded-lg">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-green-900">Paid Amount</p>
                                <p class="text-2xl font-bold text-green-600">₹{{ number_format($feeStatistics['paid_amount'] ?? 0, 2) }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Outstanding -->
                    <div class="p-4 bg-yellow-50 rounded-lg">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-yellow-900">Outstanding</p>
                                <p class="text-2xl font-bold text-yellow-600">₹{{ number_format($feeStatistics['outstanding'] ?? 0, 2) }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Overdue -->
                    <div class="p-4 bg-red-50 rounded-lg">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-red-900">Overdue</p>
                                <p class="text-2xl font-bold text-red-600">₹{{ number_format($feeStatistics['overdue_amount'] ?? 0, 2) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Next Due Information -->
                @if($feeStatistics['next_due_date'] ?? null)
                    <div class="mt-4 p-4 bg-orange-50 border border-orange-200 rounded-lg">
                        <div class="flex items-center">
                            <svg class="h-6 w-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-orange-900">Next Payment Due</p>
                                <p class="text-lg font-bold text-orange-600">
                                    ₹{{ number_format($feeStatistics['next_due_amount'] ?? 0, 2) }} 
                                    due on {{ \Carbon\Carbon::parse($feeStatistics['next_due_date'])->format('M d, Y') }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Fee Installments Table -->
            <div class="bg-white rounded-lg shadow">
                {{ $this->table }}
            </div>
        @else
            <div class="p-8 text-center bg-white rounded-lg shadow">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No Children Found</h3>
                <p class="mt-1 text-sm text-gray-500">
                    No children are associated with your account. Please contact the school administration.
                </p>
            </div>
        @endif
    </div>
</x-filament-panels::page>
