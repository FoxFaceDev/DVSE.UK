@props(['type' => 'book'])
<svg {{ $attributes }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
@switch($type)
@case('home') <path d="m3 10 9-7 9 7v10a1 1 0 0 1-1 1h-5v-7H9v7H4a1 1 0 0 1-1-1Z"/> @break
@case('clock') <circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/> @break
@case('user') <circle cx="12" cy="8" r="4"/><path d="M4 21v-2a8 8 0 0 1 16 0v2"/> @break
@case('play') <rect x="3" y="4" width="18" height="16" rx="4"/><path d="m10 8 6 4-6 4Z"/> @break
@case('check') <rect x="5" y="4" width="14" height="17" rx="3"/><path d="M9 3h6v4H9zM9 13l2 2 4-4"/> @break
@case('arrow') <path d="M5 12h14m-5-5 5 5-5 5"/> @break
@default <path d="M12 6v15M3 4c4-1 6 0 9 2 3-2 5-3 9-2v14c-4-1-6 0-9 2-3-2-5-3-9-2Z"/>
@endswitch
</svg>
