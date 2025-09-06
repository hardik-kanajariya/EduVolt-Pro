<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Report Filters -->
        <div class="p-6 bg-white rounded-lg shadow">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Report Filters</h3>
            {{ $this->form }}
        </div>

        <!-- Report Content -->
        @if(!empty($reportData))
            @switch($selectedReport)
                @case('collection_summary')
                    @include('filament.admin.reports.collection-summary', ['data' => $reportData])
                    @break

                @case('defaulter_list')
                    @include('filament.admin.reports.defaulter-list', ['data' => $reportData])
                    @break

                @case('payment_trends')
                    @include('filament.admin.reports.payment-trends', ['data' => $reportData])
                    @break

                @case('fee_category_analysis')
                    @include('filament.admin.reports.fee-category-analysis', ['data' => $reportData])
                    @break

                @case('outstanding_analysis')
                    @include('filament.admin.reports.outstanding-analysis', ['data' => $reportData])
                    @break

                @case('monthly_comparison')
                    @include('filament.admin.reports.monthly-comparison', ['data' => $reportData])
                    @break

                @default
                    <div class="p-8 text-center bg-white rounded-lg shadow">
                        <p class="text-gray-500">Please select a report type to view data.</p>
                    </div>
            @endswitch
        @else
            <div class="p-8 text-center bg-white rounded-lg shadow">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No Data Available</h3>
                <p class="mt-1 text-sm text-gray-500">
                    Please adjust your filters and try again.
                </p>
            </div>
        @endif
    </div>
</x-filament-panels::page>
