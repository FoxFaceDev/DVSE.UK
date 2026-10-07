<?php

namespace App\Rules;

use Closure;
use DOMDocument;
use DOMElement;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;

class SafeIconFile implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! $value instanceof UploadedFile || strtolower($value->getClientOriginalExtension()) !== 'svg') {
            return;
        }

        $contents = file_get_contents($value->getRealPath());

        if (! is_string($contents) || $contents === '' || preg_match('/<!DOCTYPE|<!ENTITY/i', $contents)) {
            $fail('The :attribute must be a safe SVG file.');

            return;
        }

        $previous = libxml_use_internal_errors(true);
        $document = new DOMDocument;
        $loaded = $document->loadXML($contents, LIBXML_NONET | LIBXML_NOERROR | LIBXML_NOWARNING);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        if (! $loaded || strtolower((string) $document->documentElement?->localName) !== 'svg') {
            $fail('The :attribute must be a valid SVG file.');

            return;
        }

        $blockedElements = ['script', 'foreignobject', 'iframe', 'object', 'embed', 'audio', 'video'];

        foreach ($document->getElementsByTagName('*') as $element) {
            if (! $element instanceof DOMElement) {
                continue;
            }

            if (in_array(strtolower($element->localName), $blockedElements, true)) {
                $fail('The :attribute contains unsupported SVG content.');

                return;
            }

            foreach ($element->attributes as $attributeNode) {
                $name = strtolower($attributeNode->localName);
                $attributeValue = trim($attributeNode->value);

                if (str_starts_with($name, 'on')) {
                    $fail('The :attribute contains unsafe SVG attributes.');

                    return;
                }

                if ($name === 'href' && $attributeValue !== '' && ! str_starts_with($attributeValue, '#')) {
                    $fail('The :attribute cannot reference external SVG resources.');

                    return;
                }

                if ($name === 'style' && preg_match('/(?:url\s*\(|@import)/i', $attributeValue)) {
                    $fail('The :attribute cannot reference external SVG resources.');

                    return;
                }
            }

            if (strtolower($element->localName) === 'style' && preg_match('/(?:url\s*\(|@import)/i', $element->textContent)) {
                $fail('The :attribute cannot reference external SVG resources.');

                return;
            }
        }
    }
}
