<x-filament-panels::page>
    <form wire:submit="submit">
        {{ $this->form }}

        <div class="mt-6 flex justify-end gap-3">
            <x-filament::button
                type="submit"
                wire:click="$set('data.status', 'draft')"
                color="gray"
            >
                Save as Draft
            </x-filament::button>
            
            <x-filament::button
                type="submit"
                wire:click="$set('data.status', 'submitted')"
            >
                Submit Assignment
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
