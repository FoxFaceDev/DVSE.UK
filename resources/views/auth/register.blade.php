<x-layouts.app :showBack="true" :backUrl="route('home')" title="Register">
    <div class="max-w-md mx-auto">
        <div class="bg-white rounded-2xl p-8 shadow-lg shadow-primary/5 border border-gray-100 mb-8">
            <div class="text-center mb-8">
                <h1 class="text-2xl font-heading font-bold text-primary-dark">Create Account</h1>
                <p class="text-gray-500 mt-2 text-sm">Join DVSE.UK to start your journey</p>
            </div>

            <form action="{{ route('register') }}" method="POST" x-data="{ loading: false }" @submit="loading = true">
                @csrf

                <!-- Name -->
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5">Full Name</label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                           class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all outline-none text-gray-800 @error('name') border-red-500 @enderror"
                           placeholder="John Doe">
                    @error('name')
                        <p class="mt-1.5 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">Email Address</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required
                           class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all outline-none text-gray-800 @error('email') border-red-500 @enderror"
                           placeholder="your@email.com">
                    @error('email')
                        <p class="mt-1.5 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">Password</label>
                    <input id="password" type="password" name="password" required
                           class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all outline-none text-gray-800 @error('password') border-red-500 @enderror"
                           placeholder="••••••••">
                    @error('password')
                        <p class="mt-1.5 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div class="mb-8">
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1.5">Confirm Password</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required
                           class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all outline-none text-gray-800"
                           placeholder="••••••••">
                </div>

                <!-- Submit Button -->
                <button type="submit" 
                        class="w-full py-3.5 bg-primary hover:bg-primary-dark text-white rounded-xl font-bold text-lg shadow-md shadow-primary/20 transform active:scale-[0.98] transition-all flex items-center justify-center gap-2"
                        :disabled="loading">
                    <span x-show="!loading">Create Account</span>
                    <span x-show="loading" class="flex items-center gap-2" style="display: none;">
                        <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Creating...
                    </span>
                </button>
            </form>

            <div class="mt-8 text-center text-sm text-gray-500">
                Already have an account? 
                <a href="{{ route('login') }}" class="font-medium text-primary hover:text-primary-dark hover:underline">Sign in instead</a>
            </div>
        </div>
    </div>
</x-layouts.app>
