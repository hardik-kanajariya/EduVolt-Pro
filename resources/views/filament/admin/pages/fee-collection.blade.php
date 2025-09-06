<x-filament-panels::page>
    <div class="space-y-6">
        {{ $this->form }}
        
        @if($this->selectedStudent)
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4">Quick Actions</h3>
                <div class="flex gap-4">
                    <x-filament::button 
                        color="info" 
                        icon="heroicon-o-eye"
                        wire:click="$dispatch('open-modal', { id: 'view-student-history' })"
                    >
                        View Payment History
                    </x-filament::button>
                    
                    <x-filament::button 
                        color="warning" 
                        icon="heroicon-o-exclamation-triangle"
                    >
                        View Overdue Fees
                    </x-filament::button>
                </div>
            </div>
        @endif

        <div class="flex justify-end space-x-2">
            {{ $this->getFormActions() }}
        </div>
    </div>

    @if($this->selectedStudent)
        <x-filament::modal id="view-student-history" width="4xl">
            <x-slot name="heading">
                Payment History - {{ $this->selectedStudent->user->name }}
            </x-slot>
            
            <div class="space-y-4">
                <!-- Payment history content would go here -->
                <p class="text-gray-600">Recent payment history will be displayed here.</p>
            </div>
        </x-filament::modal>
    @endif
</x-filament-panels::page>
