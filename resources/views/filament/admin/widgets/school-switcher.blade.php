<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            School Context Switcher
        </x-slot>

        <x-slot name="description">
            Switch between schools to manage school-specific data or view all schools globally.
        </x-slot>

        <div class="space-y-4">
            @if(session('admin_school_context'))
                <div class="flex items-center space-x-2 text-sm text-blue-600 bg-blue-50 p-3 rounded-lg">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 12L8 10l1.41-1.41L10 9.17l2.59-2.58L14 8l-4 4z"/>
                    </svg>
                    <span>Currently viewing: <strong>{{ App\Models\School::find(session('admin_school_context'))->name ?? 'Unknown School' }}</strong></span>
                </div>
            @else
                <div class="flex items-center space-x-2 text-sm text-green-600 bg-green-50 p-3 rounded-lg">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012-2v-1a2 2 0 012-2h1.945M12 7c0-1.657-1.343-3-3-3s-3 1.343-3 3c0 .199.02.393.057.581 1.474.541 2.927-.882 2.927-2.581.814.814 2.073-.814 2.073-2.073-.543 1.474.882 2.927 2.581 2.927C13.393 7.02 13.199 7 13 7h-1z"/>
                    </svg>
                    <span>Currently viewing: <strong>🌍 All Schools (Global View)</strong></span>
                </div>
            @endif

            {{ $this->form }}
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
