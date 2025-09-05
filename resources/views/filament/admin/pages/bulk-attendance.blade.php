<x-filament-panels::page>
    <x-filament::section>
        <x-slot name="heading">
            Bulk Attendance Management
        </x-slot>

        <x-slot name="description">
            Mark attendance for entire class at once. Select a class and date to begin.
        </x-slot>

        <form wire:submit="save">
            {{ $this->form }}

            <x-filament-actions::group class="mt-6">
                {{ $this->saveAction }}
                {{ $this->markAllPresentAction }}
                {{ $this->resetAction }}
            </x-filament-actions::group>
        </form>
    </x-filament::section>
</x-filament-panels::page>