<x-layouts.app :showBack="true" :backUrl="route('home')" title="Contact us">
    <article class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
        <h1 class="font-heading text-2xl font-bold text-primary-dark">Contact us</h1>
        <div class="mt-5 whitespace-pre-line leading-7 text-gray-700">{{ $content }}</div>
        <div class="mt-6 flex flex-wrap gap-3">
            @if($email)<a href="mailto:{{ $email }}" class="rounded-full bg-primary px-4 py-2 text-sm font-bold text-white">Email</a>@endif
            @if($whatsapp)<a href="https://wa.me/{{ preg_replace('/\D+/', '', $whatsapp) }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 rounded-full bg-green-600 px-4 py-2 text-sm font-bold text-white"><svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M20.5 3.5A11.8 11.8 0 0 0 12.1 0C5.6 0 .3 5.3.3 11.8c0 2.1.5 4.1 1.6 5.9L.2 24l6.5-1.7a11.8 11.8 0 0 0 5.4 1.4h.1c6.5 0 11.8-5.3 11.8-11.8 0-3.2-1.2-6.1-3.5-8.4Zm-8.4 18.2c-1.7 0-3.4-.5-4.9-1.3l-.4-.2-3.9 1 1-3.8-.2-.4a9.8 9.8 0 1 1 8.4 4.7Zm5.4-7.3c-.3-.1-1.8-.9-2.1-1-.3-.1-.5-.1-.7.2l-.9 1.1c-.2.3-.5.3-.8.1-2-.9-3.3-1.7-4.6-4-.3-.6.3-.6.9-1.6.1-.2.1-.4 0-.6l-.9-2.2c-.2-.5-.5-.5-.7-.5h-.6c-.2 0-.6.1-.9.4-.3.4-1.2 1.2-1.2 2.9s1.2 3.3 1.4 3.6c.2.2 2.5 3.8 6 5.3 2.2.9 3.1 1 4.2.8.7-.1 1.8-.7 2-1.4.3-.7.3-1.3.2-1.4-.1-.2-.3-.3-.6-.4Z"/></svg>WhatsApp</a>@endif
            @foreach($socialLinks as $network => $url)<a href="{{ $url }}" target="_blank" rel="noopener" aria-label="{{ ucfirst($network) }}" class="inline-flex items-center gap-2 rounded-full border border-gray-200 px-4 py-2 text-sm font-bold capitalize text-gray-700"><svg class="h-5 w-5 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="9" stroke-width="2"/><path d="M8 12h8m-4-4v8" stroke-width="2" stroke-linecap="round"/></svg>{{ $network }}</a>@endforeach
        </div>
    </article>
</x-layouts.app>
