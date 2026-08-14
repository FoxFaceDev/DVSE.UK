<x-layouts.app :showBack="true" :backUrl="route('home')" title="About us">
    <article class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
        <h1 class="font-heading text-2xl font-bold text-primary-dark">About us</h1>
        <div class="mt-5 whitespace-pre-line leading-7 text-gray-700">{{ $content }}</div>
    </article>
</x-layouts.app>
