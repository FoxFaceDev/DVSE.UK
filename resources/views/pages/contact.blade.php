<x-layouts.app :showBack="true" :backUrl="route('home')" title="Contact us">
    <div class="space-y-5">
        <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-primary-dark via-primary to-sky-500 px-6 py-8 text-white shadow-xl shadow-primary/15">
            <div class="absolute -right-10 -top-12 h-40 w-40 rounded-full bg-white/10"></div>
            <div class="absolute -bottom-12 -left-8 h-32 w-32 rounded-full bg-sky-300/20"></div>
            <div class="relative">
                <span class="inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-white/15 ring-1 ring-white/25">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M4 5.5h16v12H7l-3 2.5V5.5Z" stroke-width="1.8" stroke-linejoin="round"/><path d="m5 7 7 5 7-5" stroke-width="1.8" stroke-linejoin="round"/></svg>
                </span>
                <p class="mt-5 text-xs font-bold uppercase tracking-[0.22em] text-sky-100">We are here to help</p>
                <h1 class="mt-2 font-heading text-3xl font-bold">Contact us</h1>
                <div class="mt-4 whitespace-pre-line text-sm leading-7 text-white/90">{{ $content }}</div>
            </div>
        </section>

        @if($email || $whatsapp)
            <section class="grid gap-3 sm:grid-cols-2" aria-label="Contact options">
                @if($email)
                    <a href="mailto:{{ $email }}" class="group flex items-center gap-4 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition hover:-translate-y-0.5 hover:border-primary/30 hover:shadow-md">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-primary/10 text-primary transition group-hover:bg-primary group-hover:text-white">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M3 6.5h18v12H3z" stroke-width="1.8" stroke-linejoin="round"/><path d="m4 8 8 6 8-6" stroke-width="1.8" stroke-linejoin="round"/></svg>
                        </span>
                        <span class="min-w-0"><span class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Email us</span><span class="mt-1 block truncate text-sm font-bold text-slate-800">{{ $email }}</span></span>
                    </a>
                @endif
                @if($whatsapp)
                    <a href="https://wa.me/{{ preg_replace('/\D+/', '', $whatsapp) }}" target="_blank" rel="noopener" class="group flex items-center gap-4 rounded-2xl border border-emerald-100 bg-white p-4 shadow-sm transition hover:-translate-y-0.5 hover:border-emerald-300 hover:shadow-md">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 transition group-hover:bg-emerald-600 group-hover:text-white">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M20.5 3.5A11.8 11.8 0 0 0 12.1 0C5.6 0 .3 5.3.3 11.8c0 2.1.5 4.1 1.6 5.9L.2 24l6.5-1.7a11.8 11.8 0 0 0 5.4 1.4h.1c6.5 0 11.8-5.3 11.8-11.8 0-3.2-1.2-6.1-3.5-8.4Zm-8.4 18.2c-1.7 0-3.4-.5-4.9-1.3l-.4-.2-3.9 1 1-3.8-.2-.4a9.8 9.8 0 1 1 8.4 4.7Zm5.4-7.3c-.3-.1-1.8-.9-2.1-1-.3-.1-.5-.1-.7.2l-.9 1.1c-.2.3-.5.3-.8.1-2-.9-3.3-1.7-4.6-4-.3-.6.3-.6.9-1.6.1-.2.1-.4 0-.6l-.9-2.2c-.2-.5-.5-.5-.7-.5h-.6c-.2 0-.6.1-.9.4-.3.4-1.2 1.2-1.2 2.9s1.2 3.3 1.4 3.6c.2.2 2.5 3.8 6 5.3 2.2.9 3.1 1 4.2.8.7-.1 1.8-.7 2-1.4.3-.7.3-1.3.2-1.4-.1-.2-.3-.3-.6-.4Z"/></svg>
                        </span>
                        <span><span class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Message us</span><span class="mt-1 block text-sm font-bold text-slate-800">WhatsApp</span></span>
                    </a>
                @endif
            </section>
        @endif

        @if(count($socialLinks))
            <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="font-heading text-lg font-bold text-slate-900">Follow DVSE.UK</h2>
                <p class="mt-1 text-sm text-slate-500">News, driving tips and platform updates.</p>
                <div class="mt-4 grid grid-cols-2 gap-3">
                    @foreach($socialLinks as $network => $url)
                        @php($label = ['x' => 'X / Twitter', 'youtube' => 'YouTube', 'linkedin' => 'LinkedIn'][$network] ?? ucfirst($network))
                        <a href="{{ $url }}" target="_blank" rel="noopener" aria-label="{{ $label }}" data-social-network="{{ $network }}" class="group flex min-h-12 items-center gap-3 rounded-xl border border-slate-200 px-3 py-2.5 text-sm font-bold text-slate-700 transition hover:-translate-y-0.5 hover:border-primary/25 hover:bg-slate-50 hover:shadow-sm">
                            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg
                                {{ $network === 'facebook' ? 'bg-[#1877F2] text-white' : '' }}
                                {{ $network === 'instagram' ? 'bg-gradient-to-br from-[#833AB4] via-[#E1306C] to-[#FCAF45] text-white' : '' }}
                                {{ in_array($network, ['tiktok', 'x']) ? 'bg-black text-white' : '' }}
                                {{ $network === 'youtube' ? 'bg-[#FF0000] text-white' : '' }}
                                {{ $network === 'linkedin' ? 'bg-[#0A66C2] text-white' : '' }}">
                                @switch($network)
                                    @case('facebook')
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M13.5 22v-9h3l.5-3.5h-3.5V7.3c0-1 .3-1.8 1.8-1.8h1.9V2.4c-.3 0-1.5-.1-2.8-.1-2.8 0-4.7 1.7-4.7 4.8v2.4H6.5V13h3.2v9h3.8Z"/></svg>
                                        @break
                                    @case('instagram')
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="5" stroke-width="2"/><circle cx="12" cy="12" r="4" stroke-width="2"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/></svg>
                                        @break
                                    @case('youtube')
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M23 7.1a3 3 0 0 0-2.1-2.2C19 4.4 12 4.4 12 4.4s-7 0-8.9.5A3 3 0 0 0 1 7.1a31 31 0 0 0-.5 4.9 31 31 0 0 0 .5 4.9 3 3 0 0 0 2.1 2.2c1.9.5 8.9.5 8.9.5s7 0 8.9-.5a3 3 0 0 0 2.1-2.2 31 31 0 0 0 .5-4.9 31 31 0 0 0-.5-4.9ZM9.7 15.4V8.6l6 3.4-6 3.4Z"/></svg>
                                        @break
                                    @case('tiktok')
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M16.8 2c.4 2.2 1.7 3.5 3.8 3.7v3.2a9 9 0 0 1-3.8-1.1v6.5a6.3 6.3 0 1 1-5.4-6.2v3.3a3.1 3.1 0 1 0 2.2 3V2h3.2Z"/></svg>
                                        @break
                                    @case('x')
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M18.9 2h3.7l-8.1 9.3L24 22h-7.4l-5.8-7.6L4.2 22H.5l8.6-9.8L0 2h7.6l5.2 6.9L18.9 2Zm-1.3 18.1h2L6.5 3.8H4.4l13.2 16.3Z"/></svg>
                                        @break
                                    @case('linkedin')
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M5.3 7.8H1.7V22h3.6V7.8ZM3.5 2A2.1 2.1 0 1 0 3.5 6.2 2.1 2.1 0 0 0 3.5 2ZM22 13.9c0-4.3-2.3-6.4-5.4-6.4-2.5 0-3.6 1.4-4.2 2.3v-2h-3.6V22h3.6v-7c0-1.9.4-3.7 2.7-3.7 2.3 0 2.3 2.1 2.3 3.8V22H22v-8.1Z"/></svg>
                                        @break
                                    @default
                                        <svg class="h-4 w-4 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M10 14a4 4 0 0 0 5.7 0l3-3a4 4 0 0 0-5.7-5.7l-1.5 1.5M14 10a4 4 0 0 0-5.7 0l-3 3a4 4 0 0 0 5.7 5.7l1.5-1.5" stroke-width="2" stroke-linecap="round"/></svg>
                                @endswitch
                            </span>
                            <span class="truncate">{{ $label }}</span>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif
    </div>
</x-layouts.app>
