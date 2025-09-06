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

        <!-- Payment Summary -->
        @if($selectedChild)
            <div class="p-4 bg-white rounded-lg shadow">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Payment Summary for {{ $selectedChild->name }}</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <!-- Total Payments -->
                    <div class="p-4 bg-blue-50 rounded-lg">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-blue-900">Total Payments</p>
                                <p class="text-2xl font-bold text-blue-600">{{ $paymentSummary['total_payments'] ?? 0 }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Total Amount Paid -->
                    <div class="p-4 bg-green-50 rounded-lg">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-green-900">Total Paid</p>
                                <p class="text-2xl font-bold text-green-600">₹{{ number_format($paymentSummary['total_amount_paid'] ?? 0, 2) }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- This Month -->
                    <div class="p-4 bg-purple-50 rounded-lg">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-purple-900">This Month</p>
                                <p class="text-2xl font-bold text-purple-600">₹{{ number_format($paymentSummary['amount_this_month'] ?? 0, 2) }}</p>
                                <p class="text-xs text-purple-700">{{ $paymentSummary['payments_this_month'] ?? 0 }} payments</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Last Payment Information -->
                @if($paymentSummary['last_payment_date'] ?? null)
                    <div class="mt-4 p-4 bg-indigo-50 border border-indigo-200 rounded-lg">
                        <div class="flex items-center">
                            <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-indigo-900">Last Payment</p>
                                <p class="text-lg font-bold text-indigo-600">
                                    ₹{{ number_format($paymentSummary['last_payment_amount'] ?? 0, 2) }} 
                                    on {{ \Carbon\Carbon::parse($paymentSummary['last_payment_date'])->format('M d, Y') }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Payment History Table -->
            <div class="bg-white rounded-lg shadow">
                {{ $this->table }}
            </div>
        @else
            <div class="p-8 text-center bg-white rounded-lg shadow">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No Children Found</h3>
                <p class="mt-1 text-sm text-gray-500">
                    No children are associated with your account. Please contact the school administration.
                </p>
            </div>
        @endif
    </div>

    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('print-receipt', (event) => {
                // Open receipt in new window for printing
                const receiptId = event.receiptId;
                const printWindow = window.open(`/parent/receipt/${receiptId}`, '_blank', 'width=800,height=600');
                if (printWindow) {
                    printWindow.onload = function() {
                        printWindow.print();
                    };
                }
            });
        });
    </script>
</x-filament-panels::page>
