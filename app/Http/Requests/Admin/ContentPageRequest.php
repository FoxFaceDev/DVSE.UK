<?php

namespace App\Http\Requests\Admin;

use App\Models\ContentPage;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class ContentPageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('admin')->check();
    }

    public function rules(): array
    {
        $isMotorwaySign = $this->input('type') === ContentPage::TYPE_MOTORWAY_SIGN;
        $isCgiClips = $this->input('type') === ContentPage::TYPE_CGI_CLIPS;

        return [
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'type' => ['required', Rule::in([
                ContentPage::TYPE_CGI_CLIPS,
                ContentPage::TYPE_MOTORWAY_SIGN,
            ])],
            'text_en' => ['nullable', 'string', 'max:10000'],
            'text_ku' => ['nullable', 'string', 'max:10000'],
            'hazard_windows' => [$isCgiClips ? 'required' : 'nullable', 'array', 'min:1', 'max:20'],
            'hazard_windows.*.start' => ['required', 'numeric', 'min:0'],
            'hazard_windows.*.end' => ['required', 'numeric', 'min:0.01'],
            'hazard_windows.*.points' => ['required', 'integer', 'min:1', 'max:100'],
            'clips' => ['nullable', 'array', 'size:2'],
            'clips.*.media' => [
                'nullable',
                'file',
                'mimetypes:video/mp4,video/webm,video/ogg,video/quicktime',
                // Laravel file sizes are measured in kilobytes. 1 GiB = 1,048,576 KB.
                'max:1048576',
            ],
            'clips.*.media_url' => ['prohibited'],
            'clips.*.remove' => ['nullable', 'boolean'],
            'sign_image' => [
                $isMotorwaySign && $this->signImageIsRequired() ? 'required' : 'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:10240',
            ],
            'explanation_en' => $isMotorwaySign
                ? ['required', 'string', 'max:10000']
                : ['nullable', 'string', 'max:10000'],
            'explanation_ku' => ['nullable', 'string', 'max:10000'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($this->input('type') !== ContentPage::TYPE_CGI_CLIPS) {
                return;
            }

            foreach ($this->input('hazard_windows', []) as $index => $window) {
                $start = isset($window['start']) && is_numeric($window['start']) ? (float) $window['start'] : null;
                $end = isset($window['end']) && is_numeric($window['end']) ? (float) $window['end'] : null;

                if ($start !== null && $end !== null && $end <= $start) {
                    $validator->errors()->add(
                        "hazard_windows.$index.end",
                        'The hazard end time must be after its start time.'
                    );
                }
            }

            $contentPage = $this->route('content_page');
            $existingClips = $contentPage instanceof ContentPage
                ? $contentPage->clips->keyBy('slot')
                : collect();
            $hasClip = false;

            foreach (range(0, 1) as $slot) {
                $uploadedClip = $this->file("clips.$slot.media");
                $removeClip = $this->boolean("clips.$slot.remove");

                if ($uploadedClip) {
                    $hasClip = true;

                    continue;
                }

                if (! $removeClip && $existingClips->has($slot)) {
                    $hasClip = true;
                } elseif (! $uploadedClip) {
                    $validator->errors()->add(
                        "clips.$slot.media",
                        $slot === 0 ? 'Upload the hazard video.' : 'Upload the explanation video.'
                    );
                }
            }

            if (! $hasClip) {
                $validator->errors()->add('clips', 'Upload both the hazard video and the explanation video.');
            }
        });
    }

    public function attributes(): array
    {
        return [
            'category_id' => 'category',
            'text_en' => 'English text',
            'text_ku' => 'Kurdish text',
            'hazard_windows' => 'hazard scoring ranges',
            'hazard_windows.*.start' => 'hazard start time',
            'hazard_windows.*.end' => 'hazard end time',
            'hazard_windows.*.points' => 'hazard points',
            'sign_image' => 'motorway sign image',
            'explanation_en' => 'English explanation',
            'explanation_ku' => 'Kurdish explanation',
            'clips.*.media' => 'CGI clip',
            'clips.*.media_url' => 'CGI clip URL',
        ];
    }

    private function signImageIsRequired(): bool
    {
        $contentPage = $this->route('content_page');

        return ! ($contentPage instanceof ContentPage && $contentPage->getRawOriginal('sign_image_path'));
    }
}
