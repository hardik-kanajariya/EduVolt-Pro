@if(config('app.demo_mode'))
    <section class="py-16 bg-gradient-to-r from-blue-50 to-purple-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <div class="inline-flex items-center bg-blue-100 text-blue-800 px-4 py-2 rounded-full text-sm font-semibold mb-4">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    Demo Mode Active
                </div>
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Try EduVault Pro with Demo Credentials</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Experience all features with pre-configured demo accounts. Login forms are automatically filled for easy testing.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach(\App\Services\DemoCredentialsService::getAllCredentials() as $panelName => $credentials)
                    <div class="bg-white rounded-xl p-6 shadow-lg border-2 border-transparent hover:border-blue-200 transition-all duration-300">
                        <div class="text-center">
                            <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gradient-to-r 
                                @if($loop->index == 0) from-blue-400 to-blue-600
                                @elseif($loop->index == 1) from-green-400 to-green-600
                                @elseif($loop->index == 2) from-purple-400 to-purple-600
                                @elseif($loop->index == 3) from-orange-400 to-orange-600
                                @else from-gray-400 to-gray-600
                                @endif
                                flex items-center justify-center">
                                @if($loop->index == 0)
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                @elseif($loop->index == 1)
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                @elseif($loop->index == 2)
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path>
                                    </svg>
                                @elseif($loop->index == 3)
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                    </svg>
                                @else
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                @endif
                            </div>
                            
                            <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $panelName }}</h3>
                            <p class="text-gray-600 mb-4">{{ $credentials['name'] }}</p>
                            
                            <div class="bg-gray-50 rounded-lg p-3 mb-4">
                                <div class="text-sm text-gray-700 space-y-1">
                                    <div class="font-mono text-xs">
                                        <span class="text-gray-500">Email:</span> {{ $credentials['email'] }}
                                    </div>
                                    <div class="font-mono text-xs">
                                        <span class="text-gray-500">Password:</span> {{ $credentials['password'] }}
                                    </div>
                                </div>
                            </div>
                            
                            @php
                                $panelRoute = match($panelName) {
                                    'Admin Panel' => 'filament.admin.auth.login',
                                    'Faculty Panel' => 'filament.faculty.auth.login',
                                    'Student Panel' => 'filament.student.auth.login',
                                    'Parent Panel' => 'filament.parent.auth.login',
                                    'School Panel' => 'filament.school.auth.login',
                                    default => 'filament.admin.auth.login'
                                };
                            @endphp
                            
                            <a href="{{ route($panelRoute) }}" class="
                                @if($loop->index == 0) bg-blue-600 hover:bg-blue-700
                                @elseif($loop->index == 1) bg-green-600 hover:bg-green-700
                                @elseif($loop->index == 2) bg-purple-600 hover:bg-purple-700
                                @elseif($loop->index == 3) bg-orange-600 hover:bg-orange-700
                                @else bg-gray-600 hover:bg-gray-700
                                @endif
                                text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors duration-200 inline-block w-full">
                                Login as {{ $credentials['role'] }}
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div class="text-center mt-12">
                <div class="bg-white rounded-xl p-6 shadow-lg max-w-2xl mx-auto">
                    <div class="flex items-center justify-center space-x-2 text-blue-600 mb-3">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="font-semibold">Auto-Fill Feature</span>
                    </div>
                    <p class="text-gray-600">
                        When you click any login link above, the login form will be automatically filled with the demo credentials. 
                        Simply click the login button to access the respective panel.
                    </p>
                </div>
            </div>
        </div>
    </section>
@endif
