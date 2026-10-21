<x-layouts.admin title="Hazard Perception Page">
    <form method="POST" action="{{ route('admin.hazard-page.update') }}" class="mx-auto max-w-5xl space-y-6">
        @csrf @method('PUT')
        @if(session('success'))<div role="status" class="rounded-lg border border-green-200 bg-green-50 p-4 text-green-700">{{ session('success') }}</div>@endif
        @if($errors->any())
            <div role="alert" class="rounded-lg border border-red-200 bg-red-50 p-4 text-red-700"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
        @endif
        <section class="rounded-xl border bg-white p-6 shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <h2 class="text-lg font-bold">Hazard Perception page</h2>
                <a href="{{ route('v2.hazards') }}" target="_blank" rel="noopener" class="text-sm font-bold text-primary hover:underline">Preview page</a>
            </div>
            <p class="mt-2 text-sm text-slate-600">These settings appear when learners open Hazard Perception. Categories, clips, and mock tests are managed in their existing admin sections.</p>
            <label for="hazard-title" class="mt-4 block text-sm font-semibold">Page title</label>
            <input id="hazard-title" name="title" value="{{ old('title', $settings['title']) }}" maxlength="80" required class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2">
        </section>
        <section class="rounded-xl border bg-white p-6 shadow-sm">
            <h2 class="text-lg font-bold">Instructor banner</h2>
            <input type="hidden" name="banner_active" value="0">
            <label class="mt-3 flex items-center gap-2 text-sm font-semibold"><input type="checkbox" name="banner_active" value="1" @checked(old('banner_active', $settings['banner_active']))>Show banner</label>
            <label for="banner-destination" class="mt-4 block text-sm font-semibold">Button destination</label>
            <select id="banner-destination" name="banner_destination" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2">
                @foreach($destinations as $key => $destination)<option value="{{ $key }}" @selected(old('banner_destination', $settings['banner_destination']) === $key)>{{ $destination['label'] }}</option>@endforeach
            </select>
            <div class="mt-5 grid gap-6 md:grid-cols-2">
                @foreach(['en' => 'English', 'ku' => 'Kurdish (Sorani)'] as $language => $label)
                    <fieldset class="space-y-3 rounded-lg border bg-slate-50 p-4">
                        <legend class="px-2 font-bold">{{ $label }}</legend>
                        @foreach(['before' => 'Title before highlight', 'highlight' => 'Highlighted title', 'after' => 'Title after highlight', 'button' => 'Button text'] as $field => $fieldLabel)
                            <div>
                                <label for="banner-{{ $language }}-{{ $field }}" class="block text-sm font-semibold">{{ $fieldLabel }}</label>
                                <input id="banner-{{ $language }}-{{ $field }}" name="banner[{{ $language }}][{{ $field }}]" value="{{ old("banner.$language.$field", $settings['banner'][$language][$field]) }}" maxlength="{{ $field === 'button' ? 60 : 80 }}" @required(in_array($field, ['highlight', 'button'])) dir="{{ $language === 'ku' ? 'rtl' : 'ltr' }}" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2">
                            </div>
                        @endforeach
                    </fieldset>
                @endforeach
            </div>
        </section>
        <section class="space-y-4">
            <div><h2 class="text-lg font-bold">Menu cards</h2><p class="text-sm text-slate-600">Lower order numbers appear first. Turn a card off to hide it.</p></div>
            @foreach($settings['cards'] as $key => $card)
                <fieldset class="rounded-xl border bg-white p-5 shadow-sm">
                    <legend class="px-2 font-bold">{{ $card['label'] }}</legend>
                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        <div><label for="card-{{ $key }}-label" class="block text-sm font-semibold">Label</label><input id="card-{{ $key }}-label" name="cards[{{ $key }}][label]" value="{{ old("cards.$key.label", $card['label']) }}" maxlength="80" required class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2"></div>
                        <div><label for="card-{{ $key }}-destination" class="block text-sm font-semibold">Destination</label><select id="card-{{ $key }}-destination" name="cards[{{ $key }}][destination]" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2">@foreach($destinations as $value => $destination)<option value="{{ $value }}" @selected(old("cards.$key.destination", $card['destination']) === $value)>{{ $destination['label'] }}</option>@endforeach</select></div>
                        <div><label for="card-{{ $key }}-icon" class="block text-sm font-semibold">Icon</label><select id="card-{{ $key }}-icon" name="cards[{{ $key }}][icon]" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2">@foreach(\App\Support\HazardPage::ICONS as $icon)<option value="{{ $icon }}" @selected(old("cards.$key.icon", $card['icon']) === $icon)>{{ ucfirst($icon) }}</option>@endforeach</select></div>
                        <div><label for="card-{{ $key }}-badge" class="block text-sm font-semibold">Badge (optional)</label><input id="card-{{ $key }}-badge" name="cards[{{ $key }}][badge]" value="{{ old("cards.$key.badge", $card['badge']) }}" maxlength="16" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2"></div>
                        <div><label for="card-{{ $key }}-position" class="block text-sm font-semibold">Order</label><input id="card-{{ $key }}-position" type="number" name="cards[{{ $key }}][position]" value="{{ old("cards.$key.position", $card['position']) }}" min="1" max="99" required class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2"></div>
                        <div><label for="card-{{ $key }}-color" class="block text-sm font-semibold">Icon tile color</label><input id="card-{{ $key }}-color" type="color" name="cards[{{ $key }}][color]" value="{{ old("cards.$key.color", $card['color']) }}" class="mt-1 h-10 w-full rounded-lg border border-slate-300 p-1"></div>
                    </div>
                    <input type="hidden" name="cards[{{ $key }}][active]" value="0">
                    <label class="mt-4 flex items-center gap-2 text-sm font-semibold"><input type="checkbox" name="cards[{{ $key }}][active]" value="1" @checked(old("cards.$key.active", $card['active']))>Show card</label>
                </fieldset>
            @endforeach
        </section>
        <button type="submit" class="rounded-lg bg-primary px-6 py-3 font-bold text-white">Save Hazard Perception page</button>
    </form>
</x-layouts.admin>
