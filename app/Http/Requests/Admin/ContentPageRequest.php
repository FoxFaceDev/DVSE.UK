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

        return [
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'type' => ['required', Rule::in([
                ContentPage::TYPE_CGI_CLIPS,
                ContentPage::TYPE_MOTORWAY_SIGN,
            ])],
            'text_en' => ['nullable', 'string', 'max:10000'],
            'text_ku' => ['nullable', 'string', 'max:10000'],
            'clips' => ['nullable', 'array', 'max:4'],
            'clips.*.media' => [
                'nullable',
                'file',
                'mimetypes:video/mp4,video/webm,video/ogg,video/quicktime,image/gif',
                'max:102400',
            ],
            'clips.*.media_url' => ['nullable', 'url:http,https', 'max:2048'],
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

            $contentPage = $this->route('content_page');
            $existingClips = $contentPage instanceof ContentPage
                ? $contentPage->clips->keyBy('slot')
                : collect();
            $hasClip = false;

            foreach (range(0, 3) as $slot) {
                $uploadedClip = $this->file("clips.$slot.media");
                $clipUrl = trim((string) $this->input("clips.$slot.media_url", ''));
                $removeClip = $this->boolean("clips.$slot.remove");

                if ($uploadedClip && $clipUrl !== '') {
                    $validator->errors()->add(
                        "clips.$slot.media",
                        'Choose either an uploaded clip or a URL for this slot, not both.'
                    );
                }

                if ($uploadedClip || $clipUrl !== '') {
                    $hasClip = true;

                    continue;
                }

                if (! $removeClip && $existingClips->has($slot)) {
                    $hasClip = true;
                }
            }

            if (! $hasClip) {
                $validator->errors()->add('clips', 'Add at least one CGI clip. You can add up to four.');
            }
        });
    }

    public function attributes(): array
    {
        return [
            'category_id' => 'category',
            'text_en' => 'English text',
            'text_ku' => 'Kurdish text',
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
