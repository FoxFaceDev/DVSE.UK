<x-layouts.app :showBack="true" :backUrl="route('home')" title="My Test History">
    <div class="max-w-md mx-auto space-y-6">
        <div class="text-center mb-6">
            <div class="w-16 h-16 bg-primary/10 text-primary rounded-full flex items-center justify-center mx-auto mb-4 border border-primary/20">
                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
            <h1 class="font-heading font-bold text-2xl text-primary-dark">My Test History</h1>
            <p class="text-gray-500 mt-2 text-sm">Review your past mock test performances.</p>
        </div>

        @if($histories->isEmpty())
            <div class="bg-white p-8 text-center rounded-2xl shadow-sm border border-gray-100">
                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-gray-100">
                    <svg class="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <h3 class="text-lg font-bold text-gray-800 mb-2">No History Yet</h3>
                <p class="text-gray-500 text-sm mb-6">You haven't completed any mock tests while logged in.</p>
                <a href="{{ route('home') }}" class="inline-block px-6 py-2.5 bg-primary hover:bg-primary-dark text-white rounded-xl font-bold transition-colors">
                    Take a Mock Test
                </a>
            </div>
        @else
            <div class="space-y-4">
                @foreach($histories as $history)
                    <div class="bg-white p-5 rounded-2xl shadow-[0_4px_14px_0_rgba(0,0,0,0.03)] border border-gray-100 flex items-center gap-4 transition-all hover:border-primary/30 group">
                        
                        <!-- Status Icon -->
                        <div class="w-12 h-12 rounded-full flex-shrink-0 flex items-center justify-center {{ $history->passed ? 'bg-green-50 text-success border border-green-100' : 'bg-red-50 text-error border border-red-100' }}">
                            @if($history->passed)
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            @else
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            @endif
                        </div>

                        <!-- Info -->
                        <div class="flex-1">
                            <h3 class="font-bold text-gray-900 mb-0.5">Mock Test</h3>
                            <p class="text-xs text-gray-500 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                {{ $history->created_at->format('M d, Y - h:i A') }}
                            </p>
                        </div>

                        <!-- Score -->
                        <div class="text-right">
                            <div class="text-lg font-black {{ $history->passed ? 'text-success' : 'text-error' }}">
                                {{ $history->score }}<span class="text-sm text-gray-400 font-medium">/{{ $history->total_questions }}</span>
                            </div>
                            <div class="text-[10px] font-bold uppercase tracking-wider {{ $history->passed ? 'text-success' : 'text-error' }}">
                                {{ $history->passed ? 'Passed' : 'Failed' }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-layouts.app>
