<?php

use App\Rules\CardColor;

test('card color codes normalize to six digit hex', function ($input, $hex) {
    expect(CardColor::normalize($input))->toBe($hex);
})->with([
    ['#F7B500', '#f7b500'], ['abc', '#aabbcc'], ['#0f0', '#00ff00'],
    ['rgb(247, 181, 0)', '#f7b500'], ['RGB(255 0 128)', '#ff0080'],
    ['rgb(100%, 0%, 0%)', '#ff0000'], ['hsl(120, 100%, 50%)', '#00ff00'],
    ['hsl(-120deg 100% 50%)', '#0000ff'], ['hsl(360, 0%, 50%)', '#808080'],
]);

test('invalid card color codes are rejected', function ($input) {
    expect(CardColor::normalize($input))->toBeNull();
})->with(['rgb(256, 0, 0)', 'rgb(-1 0 0)', 'hsl(0, 101%, 50%)', '#gggggg', 'red; display:none', 'rgba(0,0,0,0.5)', '', null, ['red']]);
