<x-layouts.admin title="Website Settings">
    <form method="POST" action="{{ route('admin.site-settings.update') }}" class="mx-auto max-w-4xl space-y-6">
        @csrf @method('PUT')
        @if(session('success'))<div class="rounded-lg border border-green-200 bg-green-50 p-4 text-green-700">{{ session('success') }}</div>@endif
        @if($errors->any())<div class="rounded-lg border border-red-200 bg-red-50 p-4 text-red-700">Please correct the highlighted fields.</div>@endif
        <section class="rounded-lg border bg-white p-6 shadow-sm">
            <h3 class="text-lg font-bold">Registration privacy policy</h3>
            <p class="mb-3 mt-1 text-sm text-gray-500">Shown beside the required checkbox on account creation.</p>
            <textarea name="privacy_policy" rows="8" required class="w-full rounded-md border-gray-300">{{ old('privacy_policy', $settings['privacy_policy']) }}</textarea>
            @error('privacy_policy')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </section>
        <section class="rounded-lg border bg-white p-6 shadow-sm">
            <h3 class="text-lg font-bold">About and contact pages</h3>
            <label class="mt-4 block text-sm font-semibold">About us</label>
            <textarea name="about_us" rows="8" required class="mt-1 w-full rounded-md border-gray-300">{{ old('about_us', $settings['about_us']) }}</textarea>
            <label class="mt-4 block text-sm font-semibold">Contact us</label>
            <textarea name="contact_us" rows="8" required class="mt-1 w-full rounded-md border-gray-300">{{ old('contact_us', $settings['contact_us']) }}</textarea>
            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                <div><label class="block text-sm font-semibold">Contact email</label><input type="email" name="contact_email" value="{{ old('contact_email', $settings['contact_email']) }}" class="mt-1 w-full rounded-md border-gray-300"></div>
                <div><label class="block text-sm font-semibold">WhatsApp number</label><input name="whatsapp_number" value="{{ old('whatsapp_number', $settings['whatsapp_number']) }}" placeholder="+447700900000" class="mt-1 w-full rounded-md border-gray-300"></div>
            </div>
        </section>
        <section class="rounded-lg border bg-white p-6 shadow-sm">
            <h3 class="text-lg font-bold">Social media links</h3>
            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                @foreach(['facebook' => 'Facebook', 'instagram' => 'Instagram', 'youtube' => 'YouTube', 'tiktok' => 'TikTok', 'x' => 'X / Twitter', 'linkedin' => 'LinkedIn'] as $key => $label)
                    <div><label class="block text-sm font-semibold">{{ $label }}</label><input type="url" name="social_links[{{ $key }}]" value="{{ old("social_links.$key", $settings['social_links'][$key] ?? '') }}" placeholder="https://" class="mt-1 w-full rounded-md border-gray-300"></div>
                @endforeach
            </div>
        </section>
        <button class="rounded-lg bg-primary px-6 py-3 font-bold text-white">Save website settings</button>
    </form>
</x-layouts.admin>
