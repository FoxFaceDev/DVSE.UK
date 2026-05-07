<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - DVSE.UK</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Lexend:wght@400;600;700&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body {
            background: radial-gradient(circle at top left, #1e293b, #0f172a);
        }
        .glass-panel {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .input-focus:focus {
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.5);
            border-color: #3b82f6;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 antialiased font-body">

    <div class="w-full max-w-md" x-data="{ loading: false }">
        <!-- Logo/Header Area -->
        <div class="text-center mb-10">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-primary shadow-lg mb-4">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
            </div>
            <h1 class="text-3xl font-heading font-bold text-white tracking-tight">Admin Portal</h1>
            <p class="text-gray-400 mt-2">Welcome back! Please sign in to continue.</p>
        </div>

        <!-- Login Card -->
        <div class="glass-panel rounded-3xl p-8 shadow-2xl">
            <form action="{{ route('admin.login') }}" method="POST" @submit="loading = true">
                @csrf

                <!-- Email Address -->
                <div class="mb-6">
                    <label for="email" class="block text-sm font-medium text-gray-300 mb-2">Email Address</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206" />
                            </svg>
                        </span>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                               class="w-full pl-10 pr-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-gray-500 outline-none transition-all input-focus @error('email') border-red-500 @enderror"
                               placeholder="admin@dvse.uk">
                    </div>
                    @error('email')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div class="mb-6">
                    <label for="password" class="block text-sm font-medium text-gray-300 mb-2">Password</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </span>
                        <input id="password" type="password" name="password" required
                               class="w-full pl-10 pr-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-gray-500 outline-none transition-all input-focus @error('password') border-red-500 @enderror"
                               placeholder="••••••••">
                    </div>
                    @error('password')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between mb-8">
                    <label class="flex items-center text-sm text-gray-400 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded border-white/10 bg-white/5 text-primary focus:ring-primary focus:ring-offset-0 mr-2">
                        <span>Remember me</span>
                    </label>
                    <a href="#" class="text-sm font-medium text-primary hover:text-primary-light transition-colors">Forgot password?</a>
                </div>

                <!-- Submit Button -->
                <button type="submit" 
                        class="w-full py-4 bg-primary hover:bg-primary-dark text-white rounded-xl font-bold text-lg shadow-lg shadow-primary/20 transform active:scale-[0.98] transition-all flex items-center justify-center gap-2"
                        :disabled="loading">
                    <span x-show="!loading">Sign In</span>
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
        <p class="text-center mt-8 text-gray-500 text-sm">
            &copy; {{ date('Y') }} DVSE.UK Administration. All rights reserved.
        </p>
    </div>

</body>
</html>
