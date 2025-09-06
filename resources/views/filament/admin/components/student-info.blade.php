<div class="bg-gray-50 rounded-lg p-4">
    <div class="grid grid-cols-2 gap-4">
        <div>
            <h4 class="font-semibold text-gray-900">{{ $student->user->name }}</h4>
            <p class="text-sm text-gray-600">Admission No: {{ $student->admission_number }}</p>
            <p class="text-sm text-gray-600">Class: {{ $student->schoolClass->name }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-600">Parent: {{ $student->parent_name }}</p>
            <p class="text-sm text-gray-600">Phone: {{ $student->parent_phone }}</p>
            <p class="text-sm text-gray-600">Email: {{ $student->parent_email }}</p>
        </div>
    </div>
    
    @php
        $totalDue = collect($pendingInstallments ?? [])->sum('amount');
        $overdueCount = collect($pendingInstallments ?? [])->where('is_overdue', true)->count();
    @endphp
    
    <div class="mt-4 pt-4 border-t border-gray-200">
        <div class="flex justify-between items-center">
            <div class="flex space-x-4">
                <span class="text-sm">
                    <span class="font-medium">Total Due:</span> 
                    <span class="text-green-600 font-semibold">${{ number_format($totalDue, 2) }}</span>
                </span>
                @if($overdueCount > 0)
                    <span class="text-sm">
                        <span class="font-medium text-red-600">Overdue:</span> 
                        <span class="text-red-600 font-semibold">{{ $overdueCount }} installments</span>
                    </span>
                @endif
            </div>
            <div class="flex space-x-2">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $student->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    {{ ucfirst($student->status) }}
                </span>
            </div>
        </div>
    </div>
</div>
