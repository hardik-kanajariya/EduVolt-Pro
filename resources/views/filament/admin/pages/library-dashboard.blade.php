<x-filament-panels::page>
    <div class="grid gap-6">
        {{-- Statistics Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Total Books --}}
            <x-filament::card>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Books</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalBooks }}</p>
                    </div>
                    <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-full">
                        <x-heroicon-o-book-open class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                    </div>
                </div>
            </x-filament::card>

            {{-- Available Books --}}
            <x-filament::card>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Available Books</p>
                        <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $availableBooks }}</p>
                    </div>
                    <div class="p-3 bg-green-100 dark:bg-green-900 rounded-full">
                        <x-heroicon-o-check-circle class="w-6 h-6 text-green-600 dark:text-green-400" />
                    </div>
                </div>
            </x-filament::card>

            {{-- Issued Books --}}
            <x-filament::card>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Issued Books</p>
                        <p class="text-2xl font-bold text-orange-600 dark:text-orange-400">{{ $issuedBooks }}</p>
                    </div>
                    <div class="p-3 bg-orange-100 dark:bg-orange-900 rounded-full">
                        <x-heroicon-o-arrow-right-circle class="w-6 h-6 text-orange-600 dark:text-orange-400" />
                    </div>
                </div>
            </x-filament::card>

            {{-- Overdue Books --}}
            <x-filament::card>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Overdue Books</p>
                        <p class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $overdueBooks }}</p>
                    </div>
                    <div class="p-3 bg-red-100 dark:bg-red-900 rounded-full">
                        <x-heroicon-o-exclamation-circle class="w-6 h-6 text-red-600 dark:text-red-400" />
                    </div>
                </div>
            </x-filament::card>
        </div>

        {{-- Main Content Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Recent Issues --}}
            <x-filament::card>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Issues</h3>
                    <x-filament::link :href="route('filament.admin.resources.book-issues.index')" color="primary">
                        View All
                    </x-filament::link>
                </div>

                <div class="space-y-3">
                    @forelse($recentIssues as $issue)
                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                        <div class="flex-1">
                            <p class="font-medium text-gray-900 dark:text-white">{{ $issue->book->title }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $issue->student->name }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-500">{{ $issue->issue_date->format('M j, Y') }}</p>
                        </div>
                        <x-filament::badge color="warning">
                            Due: {{ $issue->due_date->format('M j') }}
                        </x-filament::badge>
                    </div>
                    @empty
                    <p class="text-gray-500 dark:text-gray-400 text-center py-4">No recent issues</p>
                    @endforelse
                </div>
            </x-filament::card>

            {{-- Popular Books --}}
            <x-filament::card>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Popular Books</h3>
                    <x-filament::link :href="route('filament.admin.resources.library-books.index')" color="primary">
                        View All
                    </x-filament::link>
                </div>

                <div class="space-y-3">
                    @forelse($popularBooks as $book)
                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                        <div class="flex-1">
                            <p class="font-medium text-gray-900 dark:text-white">{{ $book->title }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $book->author }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-500">{{ $book->category->name ?? 'Uncategorized' }}</p>
                        </div>
                        <x-filament::badge color="success">
                            {{ $book->issues_count }} issues
                        </x-filament::badge>
                    </div>
                    @empty
                    <p class="text-gray-500 dark:text-gray-400 text-center py-4">No data available</p>
                    @endforelse
                </div>
            </x-filament::card>
        </div>

        {{-- Quick Actions --}}
        <x-filament::card>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Quick Actions</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <x-filament::button
                    :href="route('filament.admin.pages.issue-book')"
                    color="success"
                    size="lg"
                    icon="heroicon-o-plus"
                    class="justify-center">
                    Issue New Book
                </x-filament::button>

                <x-filament::button
                    :href="route('filament.admin.pages.return-book')"
                    color="warning"
                    size="lg"
                    icon="heroicon-o-arrow-left"
                    class="justify-center">
                    Return Book
                </x-filament::button>

                <x-filament::button
                    :href="route('filament.admin.pages.library-reports')"
                    color="info"
                    size="lg"
                    icon="heroicon-o-document-chart-bar"
                    class="justify-center">
                    View Reports
                </x-filament::button>
            </div>
        </x-filament::card>
    </div>
</x-filament-panels::page>