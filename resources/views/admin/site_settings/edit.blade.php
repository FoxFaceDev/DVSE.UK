<x-layouts.admin title="Website Settings">
    <form method="POST" action="{{ route('admin.site-settings.update') }}" class="mx-auto max-w-4xl space-y-6">
        @csrf @method('PUT')
        @if(session('success'))<div class="rounded-lg border border-green-200 bg-green-50 p-4 text-green-700">{{ session('success') }}</div>@endif
        @if($errors->any())<div class="rounded-lg border border-red-200 bg-red-50 p-4 text-red-700">Please correct the highlighted fields.</div>@endif
        <section class="rounded-lg border bg-white p-6 shadow-sm">
            <h3 class="text-lg font-bold">Registration privacy policy</h3>
            <p class="mb-3 mt-1 text-sm text-gray-500">Shown beside the required checkbox on account creation.</p>
            <textarea name="privacy_policy" rows="8" required class="w-full rounded-md border border-slate-300 bg-white px-3 py-2.5 shadow-sm focus:border-primary focus:ring-primary">{{ old('privacy_policy', $settings['privacy_policy']) }}</textarea>
            @error('privacy_policy')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </section>
        <section class="rounded-lg border bg-white p-6 shadow-sm">
            <h3 class="text-lg font-bold">About and contact pages</h3>
            <label class="mt-4 block text-sm font-semibold">About us</label>
            <textarea name="about_us" rows="8" required class="mt-1 w-full rounded-md border border-slate-300 bg-white px-3 py-2.5 shadow-sm focus:border-primary focus:ring-primary">{{ old('about_us', $settings['about_us']) }}</textarea>
            <label class="mt-4 block text-sm font-semibold">Contact us</label>
            <textarea name="contact_us" rows="8" required class="mt-1 w-full rounded-md border border-slate-300 bg-white px-3 py-2.5 shadow-sm focus:border-primary focus:ring-primary">{{ old('contact_us', $settings['contact_us']) }}</textarea>
            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                <div><label class="block text-sm font-semibold">Contact email</label><input type="email" name="contact_email" value="{{ old('contact_email', $settings['contact_email']) }}" class="mt-1 w-full rounded-md border border-slate-300 bg-white px-3 py-2.5 shadow-sm focus:border-primary focus:ring-primary"></div>
                <div><label class="block text-sm font-semibold">WhatsApp number</label><input name="whatsapp_number" value="{{ old('whatsapp_number', $settings['whatsapp_number']) }}" placeholder="+447700900000" class="mt-1 w-full rounded-md border border-slate-300 bg-white px-3 py-2.5 shadow-sm focus:border-primary focus:ring-primary"></div>
            </div>
        </section>
        <section class="rounded-lg border bg-white p-6 shadow-sm">
            <h3 class="text-lg font-bold">Social media links</h3>
            <p class="mt-1 text-sm text-gray-500">Turn a network off to hide it from the contact page without losing its URL.</p>
            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                @foreach(['facebook' => 'Facebook', 'instagram' => 'Instagram', 'youtube' => 'YouTube', 'tiktok' => 'TikTok', 'x' => 'X / Twitter', 'linkedin' => 'LinkedIn'] as $key => $label)
                    @php
                        $savedLink = $settings['social_links'][$key] ?? ['url' => '', 'active' => false];
                        $isActive = (bool) old("social_links.$key.active", $savedLink['active']);
                    @endphp
                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4" x-data="{ active: @js($isActive) }">
                        <div class="flex items-center justify-between gap-3">
                            <label for="social-{{ $key }}" class="text-sm font-bold text-slate-800">{{ $label }}</label>
                            <label class="inline-flex cursor-pointer items-center gap-2 text-xs font-semibold" :class="active ? 'text-emerald-700' : 'text-slate-500'">
                                <span x-text="active ? 'Active' : 'Inactive'"></span>
                                <span class="relative inline-flex h-6 w-11 items-center rounded-full transition" :class="active ? 'bg-emerald-500' : 'bg-slate-300'">
                                    <input type="hidden" name="social_links[{{ $key }}][active]" value="0">
                                    <input type="checkbox" name="social_links[{{ $key }}][active]" value="1" x-model="active" class="peer sr-only">
                                    <span class="inline-block h-4 w-4 translate-x-1 rounded-full bg-white shadow transition" :class="active && 'translate-x-6'"></span>
                                </span>
                            </label>
                        </div>
                        <input id="social-{{ $key }}" type="url" name="social_links[{{ $key }}][url]" value="{{ old("social_links.$key.url", $savedLink['url']) }}" placeholder="https://" class="mt-3 w-full rounded-md border border-slate-300 bg-white px-3 py-2.5 shadow-sm focus:border-primary focus:ring-primary">
                        @error("social_links.$key.url")<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                @endforeach
            </div>
        </section>
        <button class="rounded-lg bg-primary px-6 py-3 font-bold text-white">Save website settings</button>
    </form>
</x-layouts.admin>
