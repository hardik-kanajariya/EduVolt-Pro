<x-filament-panels::page>
    <div class="grid gap-6">
        {{-- Statistics Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4">
            {{-- Total Timetables --}}
            <x-filament::card>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Timetables</p>
                        <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $totalTimetables }}</p>
                    </div>
                    <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-full">
                        <x-heroicon-o-calendar class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                    </div>
                </div>
            </x-filament::card>

            {{-- Total Classes --}}
            <x-filament::card>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Classes</p>
                        <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $totalClasses }}</p>
                    </div>
                    <div class="p-3 bg-green-100 dark:bg-green-900 rounded-full">
                        <x-heroicon-o-user-group class="w-6 h-6 text-green-600 dark:text-green-400" />
                    </div>
                </div>
            </x-filament::card>

            {{-- Total Subjects --}}
            <x-filament::card>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Subjects</p>
                        <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ $totalSubjects }}</p>
                    </div>
                    <div class="p-3 bg-purple-100 dark:bg-purple-900 rounded-full">
                        <x-heroicon-o-book-open class="w-6 h-6 text-purple-600 dark:text-purple-400" />
                    </div>
                </div>
            </x-filament::card>

            {{-- Total Teachers --}}
            <x-filament::card>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Teachers</p>
                        <p class="text-2xl font-bold text-orange-600 dark:text-orange-400">{{ $totalTeachers }}</p>
                    </div>
                    <div class="p-3 bg-orange-100 dark:bg-orange-900 rounded-full">
                        <x-heroicon-o-academic-cap class="w-6 h-6 text-orange-600 dark:text-orange-400" />
                    </div>
                </div>
            </x-filament::card>

            {{-- Total Periods --}}
            <x-filament::card>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Periods</p>
                        <p class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $totalPeriods }}</p>
                    </div>
                    <div class="p-3 bg-red-100 dark:bg-red-900 rounded-full">
                        <x-heroicon-o-clock class="w-6 h-6 text-red-600 dark:text-red-400" />
                    </div>
                </div>
            </x-filament::card>
        </div>

        {{-- Main Content --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Recent Timetables --}}
            <x-filament::card>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Timetables</h3>
                    <x-filament::link :href="route('filament.admin.resources.timetables.timetables.index')" color="primary">
                        View All
                    </x-filament::link>
                </div>

                <div class="space-y-3">
                    @forelse($recentTimetables as $timetable)
                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                        <div class="flex-1">
                            <p class="font-medium text-gray-900 dark:text-white">{{ $timetable->class->name ?? 'N/A' }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $timetable->subject->name ?? 'N/A' }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-500">{{ $timetable->teacher->user->name ?? 'N/A' }}</p>
                        </div>
                        <div class="text-right">
                            <x-filament::badge color="info">
                                {{ $timetable->day_of_week ?? 'N/A' }}
                            </x-filament::badge>
                            <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">
                                {{ $timetable->period->start_time ?? 'N/A' }} - {{ $timetable->period->end_time ?? 'N/A' }}
                            </p>
                        </div>
                    </div>
                    @empty
                    <p class="text-gray-500 dark:text-gray-400 text-center py-4">No timetables created yet</p>
                    @endforelse
                </div>
            </x-filament::card>

            {{-- Timetable Builder Tool --}}
            <x-filament::card>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Timetable Builder</h3>
                </div>

                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-3">
                        <x-filament::button
                            :href="route('filament.admin.resources.timetables.timetables.create')"
                            color="success"
                            size="lg"
                            icon="heroicon-o-plus"
                            class="justify-center">
                            Create New
                        </x-filament::button>

                        <x-filament::button
                            :href="route('filament.admin.resources.timetables.timetables.index')"
                            color="info"
                            size="lg"
                            icon="heroicon-o-table-cells"
                            class="justify-center">
                            View All
                        </x-filament::button>
                    </div>

                    <div class="border-t pt-4 space-y-3">
                        <h4 class="font-medium text-gray-900 dark:text-white">Quick Access</h4>

                        <x-filament::button
                            :href="route('filament.admin.resources.school-classes.school-classes.index')"
                            color="gray"
                            outlined
                            icon="heroicon-o-user-group"
                            class="w-full justify-start">
                            Manage Classes
                        </x-filament::button>

                        <x-filament::button
                            :href="route('filament.admin.resources.subjects.index')"
                            color="gray"
                            outlined
                            icon="heroicon-o-book-open"
                            class="w-full justify-start">
                            Manage Subjects
                        </x-filament::button>

                        <x-filament::button
                            :href="route('filament.admin.resources.teachers.teachers.index')"
                            color="gray"
                            outlined
                            icon="heroicon-o-academic-cap"
                            class="w-full justify-start">
                            Manage Teachers
                        </x-filament::button>

                        <x-filament::button
                            :href="route('filament.admin.resources.periods.periods.index')"
                            color="gray"
                            outlined
                            icon="heroicon-o-clock"
                            class="w-full justify-start">
                            Manage Periods
                        </x-filament::button>
                    </div>
                </div>
            </x-filament::card>
        </div>

        {{-- Timetable Overview --}}
        <x-filament::card>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Weekly Timetable Overview</h3>

            <div class="overflow-x-auto">
                <div class="grid grid-cols-7 gap-2 min-w-full">
                    @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day)
                    <div class="border rounded-lg p-3 bg-gray-50 dark:bg-gray-800">
                        <h4 class="font-medium text-center mb-2 text-gray-900 dark:text-white">{{ $day }}</h4>
                        <div class="space-y-1">
                            @php
                            $dayTimetables = $recentTimetables->where('day_of_week', $day);
                            @endphp
                            @if($dayTimetables->count() > 0)
                            @foreach($dayTimetables->take(3) as $timetable)
                            <div class="text-xs p-2 bg-blue-100 dark:bg-blue-900 rounded text-center">
                                <p class="font-medium text-blue-800 dark:text-blue-200">{{ $timetable->subject->name ?? 'N/A' }}</p>
                                <p class="text-blue-600 dark:text-blue-300">{{ $timetable->class->name ?? 'N/A' }}</p>
                            </div>
                            @endforeach
                            @if($dayTimetables->count() > 3)
                            <p class="text-xs text-center text-gray-500 dark:text-gray-400">+{{ $dayTimetables->count() - 3 }} more</p>
                            @endif
                            @else
                            <p class="text-xs text-center text-gray-400">No classes</p>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </x-filament::card>
    </div>
</x-filament-panels::page>