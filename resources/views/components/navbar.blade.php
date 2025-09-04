<nav class="bg-white shadow-lg sticky top-0 z-50" x-data="{ mobileMenuOpen: false }">
    <div class="container-custom">
        <div class="flex justify-between items-center py-4">
            <!-- Logo -->
            <div class="flex items-center">
                <a href="{{ route('home') }}" class="flex items-center space-x-3">
                    <img src="{{ asset('images/logo.png') }}" alt="EduVault Pro" class="h-10 w-auto">
                    <span class="text-2xl font-bold font-heading text-primary-600">EduVault Pro</span>
                </a>
            </div>
            
            <!-- Desktop Navigation -->
            <div class="hidden lg:flex items-center space-x-8">
                <a href="{{ route('home') }}" class="text-gray-700 hover:text-primary-600 font-medium transition-colors duration-200 {{ request()->routeIs('home') ? 'text-primary-600' : '' }}">
                    Home
                </a>
                <a href="{{ route('features') }}" class="text-gray-700 hover:text-primary-600 font-medium transition-colors duration-200 {{ request()->routeIs('features') ? 'text-primary-600' : '' }}">
                    Features
                </a>
                <a href="{{ route('pricing') }}" class="text-gray-700 hover:text-primary-600 font-medium transition-colors duration-200 {{ request()->routeIs('pricing') ? 'text-primary-600' : '' }}">
                    Pricing
                </a>
                <a href="{{ route('about') }}" class="text-gray-700 hover:text-primary-600 font-medium transition-colors duration-200 {{ request()->routeIs('about') ? 'text-primary-600' : '' }}">
                    About
                </a>
                <a href="{{ route('contact') }}" class="text-gray-700 hover:text-primary-600 font-medium transition-colors duration-200 {{ request()->routeIs('contact') ? 'text-primary-600' : '' }}">
                    Contact
                </a>
            </div>
            
            <!-- CTA Buttons -->
            <div class="hidden lg:flex items-center space-x-4">
                <div class="relative" x-data="{ loginDropdown: false }">
                    <button @click="loginDropdown = !loginDropdown" class="btn-secondary flex items-center space-x-2">
                        <span>Login</span>
                        <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': loginDropdown }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    
                    <!-- Login Dropdown -->
                    <div x-show="loginDropdown" 
                         @click.away="loginDropdown = false"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 transform scale-95"
                         x-transition:enter-end="opacity-100 transform scale-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 transform scale-100"
                         x-transition:leave-end="opacity-0 transform scale-95"
                         class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg ring-1 ring-black ring-opacity-5 z-50">
                        <div class="py-1">
                            <a href="{{ route('filament.admin.auth.login') }}" class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 hover:text-primary-600 transition-colors duration-200">
                                <svg class="w-5 h-5 mr-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.031 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                </svg>
                                <div>
                                    <div class="font-medium">Admin Portal</div>
                                    <div class="text-xs text-gray-500">School Administration</div>
                                </div>
                            </a>
                            <a href="{{ route('filament.faculty.auth.login') }}" class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 hover:text-primary-600 transition-colors duration-200">
                                <svg class="w-5 h-5 mr-3 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                                <div>
                                    <div class="font-medium">Faculty Portal</div>
                                    <div class="text-xs text-gray-500">Teachers & Staff</div>
                                </div>
                            </a>
                            <a href="{{ route('filament.student.auth.login') }}" class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 hover:text-primary-600 transition-colors duration-200">
                                <svg class="w-5 h-5 mr-3 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path>
                                </svg>
                                <div>
                                    <div class="font-medium">Student Portal</div>
                                    <div class="text-xs text-gray-500">Student Dashboard</div>
                                </div>
                            </a>
                            <a href="{{ route('filament.parent.auth.login') }}" class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 hover:text-primary-600 transition-colors duration-200">
                                <svg class="w-5 h-5 mr-3 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                </svg>
                                <div>
                                    <div class="font-medium">Parent Portal</div>
                                    <div class="text-xs text-gray-500">Monitor Your Child</div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
                
                <a href="{{ route('contact') }}" class="btn-primary">
                    Get Started
                </a>
            </div>
            
            <!-- Mobile Menu Button -->
            <div class="lg:hidden">
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-gray-700 hover:text-primary-600 focus:outline-none focus:text-primary-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="!mobileMenuOpen">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="mobileMenuOpen">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
    
    <!-- Mobile Menu -->
    <div x-show="mobileMenuOpen" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 transform scale-95"
         x-transition:enter-end="opacity-100 transform scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 transform scale-100"
         x-transition:leave-end="opacity-0 transform scale-95"
         class="lg:hidden bg-white border-t border-gray-200">
        <div class="px-4 py-4 space-y-4">
            <!-- Mobile Navigation Links -->
            <a href="{{ route('home') }}" class="block text-gray-700 hover:text-primary-600 font-medium py-2 {{ request()->routeIs('home') ? 'text-primary-600' : '' }}">
                Home
            </a>
            <a href="{{ route('features') }}" class="block text-gray-700 hover:text-primary-600 font-medium py-2 {{ request()->routeIs('features') ? 'text-primary-600' : '' }}">
                Features
            </a>
            <a href="{{ route('pricing') }}" class="block text-gray-700 hover:text-primary-600 font-medium py-2 {{ request()->routeIs('pricing') ? 'text-primary-600' : '' }}">
                Pricing
            </a>
            <a href="{{ route('about') }}" class="block text-gray-700 hover:text-primary-600 font-medium py-2 {{ request()->routeIs('about') ? 'text-primary-600' : '' }}">
                About
            </a>
            <a href="{{ route('contact') }}" class="block text-gray-700 hover:text-primary-600 font-medium py-2 {{ request()->routeIs('contact') ? 'text-primary-600' : '' }}">
                Contact
            </a>
            
            <!-- Mobile Login Links -->
            <div class="pt-4 border-t border-gray-200 space-y-2">
                <a href="{{ route('filament.admin.auth.login') }}" class="block text-gray-700 hover:text-primary-600 font-medium py-2">
                    Admin Portal
                </a>
                <a href="{{ route('filament.faculty.auth.login') }}" class="block text-gray-700 hover:text-primary-600 font-medium py-2">
                    Faculty Portal
                </a>
                <a href="{{ route('filament.student.auth.login') }}" class="block text-gray-700 hover:text-primary-600 font-medium py-2">
                    Student Portal
                </a>
                <a href="{{ route('filament.parent.auth.login') }}" class="block text-gray-700 hover:text-primary-600 font-medium py-2">
                    Parent Portal
                </a>
            </div>
            
            <!-- Mobile CTA -->
            <div class="pt-4">
                <a href="{{ route('contact') }}" class="btn-primary w-full text-center">
                    Get Started
                </a>
            </div>
        </div>
    </div>
</nav>
