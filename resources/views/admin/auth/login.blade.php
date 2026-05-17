<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - DVSE.UK</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Lexend:wght@400;600;700&family=Outfit:wght@400;500;700;900&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="min-h-screen bg-gray-50 flex items-center justify-center p-4 antialiased font-body relative overflow-hidden">

    <!-- Decorative Background Shapes -->
    <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-primary/20 rounded-full blur-[100px]"></div>
    <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-cyan-400/20 rounded-full blur-[100px]"></div>

    <div class="w-full max-w-md relative z-10" x-data="{ loading: false }">
        
        <!-- Logo/Header Area -->
        <div class="text-center mb-8 flex flex-col items-center">
            <div class="bg-primary-dark p-4 rounded-2xl shadow-md border border-primary/20 mb-6 inline-block">
                <img src="{{ asset('images/logo.png') }}" alt="DVSE.UK Logo" class="h-12 w-auto object-contain">
            </div>
            <h1 class="text-3xl font-heading font-black text-gray-900 tracking-tight">Admin Portal</h1>
            <p class="text-gray-500 mt-2 font-medium">Secure access to DVSE management</p>
        </div>

        <!-- Login Card -->
        <div class="bg-white rounded-3xl p-8 shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100">
            <form action="{{ route('admin.login') }}" method="POST" @submit="loading = true">
                @csrf

                <!-- Email Address -->
                <div class="mb-5">
                    <label for="email" class="block text-sm font-bold text-gray-700 mb-2">Email Address</label>
                    <div class="relative group">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400 group-focus-within:text-primary transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206" />
                            </svg>
                        </span>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                               class="w-full pl-11 pr-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary focus:bg-white transition-all @error('email') border-red-500 bg-red-50 @enderror"
                               placeholder="admin@dvse.uk">
                    </div>
                    @error('email')
                        <p class="mt-2 text-sm text-red-500 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div class="mb-6">
                    <label for="password" class="block text-sm font-bold text-gray-700 mb-2">Password</label>
                    <div class="relative group">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400 group-focus-within:text-primary transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </span>
                        <input id="password" type="password" name="password" required
                               class="w-full pl-11 pr-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary focus:bg-white transition-all @error('password') border-red-500 bg-red-50 @enderror"
                               placeholder="••••••••">
                    </div>
                    @error('password')
                        <p class="mt-2 text-sm text-red-500 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between mb-8">
                    <label class="flex items-center text-sm text-gray-600 cursor-pointer group">
                        <div class="relative flex items-center justify-center">
                            <input type="checkbox" name="remember" class="peer sr-only">
                            <div class="w-5 h-5 rounded border-2 border-gray-300 bg-white peer-checked:bg-primary peer-checked:border-primary transition-all flex items-center justify-center mr-2">
                                <svg class="w-3 h-3 text-white opacity-0 peer-checked:opacity-100" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            </div>
                        </div>
                        <span class="font-medium group-hover:text-gray-900 transition-colors">Remember me</span>
                    </label>
                    <a href="#" class="text-sm font-bold text-primary hover:text-primary-dark transition-colors">Forgot password?</a>
                </div>

                <!-- Submit Button -->
                <button type="submit" 
                        class="w-full py-4 bg-primary hover:bg-primary-dark text-white rounded-xl font-bold text-lg shadow-[0_8px_20px_rgb(0,0,0,0.08)] transform active:scale-[0.98] transition-all flex items-center justify-center gap-2 group"
                        :disabled="loading">
                    <span x-show="!loading" class="flex items-center gap-2">
                        Sign In to Dashboard
                        <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </span>
                    <span x-show="loading" class="flex items-center gap-2">
                        <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Authenticating...
                    </span>
                </button>
            </form>
        </div>

        <!-- Footer -->
        <p class="text-center mt-8 text-gray-400 text-sm font-medium">
            &copy; {{ date('Y') }} DVSE.UK Administration. All rights reserved.
        </p>
    </div>

</body>
</html>
