<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CardColor implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (self::normalize($value) === null) {
            $fail('Enter a HEX, RGB, or HSL color, such as #f7b500, rgb(247, 181, 0), or hsl(44, 100%, 48%).');
        }
    }

    public static function normalize(mixed $value): ?string
    {
        if (! is_string($value) || strlen($value) > 100) {
            return null;
        }

        $value = strtolower(trim($value));
        if (preg_match('/^#?([a-f0-9]{6}|[a-f0-9]{3})$/', $value, $match)) {
            $hex = $match[1];
            if (strlen($hex) === 3) {
                $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
            }

            return '#'.$hex;
        }

        if (! preg_match('/^(rgb|hsl)\(\s*([^()]+)\s*\)$/', $value, $match)) {
            return null;
        }

        $parts = str_contains($match[2], ',')
            ? array_map('trim', explode(',', $match[2]))
            : preg_split('/\s+/', trim($match[2]));
        if (count($parts) !== 3) {
            return null;
        }

        if ($match[1] === 'rgb') {
            $channels = [];
            foreach ($parts as $part) {
                if (! preg_match('/^(\d+(?:\.\d+)?|\.\d+)(%)?$/', $part, $component)) {
                    return null;
                }
                $limit = isset($component[2]) ? 100 : 255;
                if ((float) $component[1] > $limit) {
                    return null;
                }
                $channels[] = (float) $component[1] * 255 / $limit;
            }
        } else {
            if (! preg_match('/^([+-]?(?:\d+(?:\.\d+)?|\.\d+))(?:deg)?$/', $parts[0], $hue)
                || ! preg_match('/^(\d+(?:\.\d+)?|\.\d+)%$/', $parts[1], $saturation)
                || ! preg_match('/^(\d+(?:\.\d+)?|\.\d+)%$/', $parts[2], $lightness)
                || (float) $saturation[1] > 100 || (float) $lightness[1] > 100) {
                return null;
            }

            $h = fmod(fmod((float) $hue[1], 360) + 360, 360) / 60;
            $s = (float) $saturation[1] / 100;
            $l = (float) $lightness[1] / 100;
            $c = (1 - abs(2 * $l - 1)) * $s;
            $x = $c * (1 - abs(fmod($h, 2) - 1));
            $m = $l - $c / 2;
            $channels = match ((int) floor($h)) {
                0 => [$c, $x, 0], 1 => [$x, $c, 0], 2 => [0, $c, $x],
                3 => [0, $x, $c], 4 => [$x, 0, $c], default => [$c, 0, $x],
            };
            $channels = array_map(fn ($channel) => ($channel + $m) * 255, $channels);
        }

        return sprintf('#%02x%02x%02x', ...array_map(fn ($channel) => (int) round($channel), $channels));
    }
}
