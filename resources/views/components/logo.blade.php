@props(['size' => 'md', 'withText' => true])

@php
$sizes = [
    'sm' => 'w-8 h-8',
    'md' => 'w-9 h-9',
    'lg' => 'w-16 h-16',
];

$iconSizes = [
    'sm' => 'w-4 h-4',
    'md' => 'w-5 h-5',
    'lg' => 'w-8 h-8',
];

$textSizes = [
    'sm' => 'text-lg',
    'md' => 'text-xl',
    'lg' => 'text-3xl',
];
@endphp

<div class="flex items-center gap-3 {{ $attributes->get('class') }}">
    <div class="{{ $sizes[$size] }} rounded-{{ $size === 'lg' ? '2xl' : 'lg' }} bg-gradient-to-br from-gym-primary to-amber-500 flex items-center justify-center shadow-lg shadow-gym-primary/20 group-hover:shadow-gym-primary/40 transition-shadow {{ $size === 'lg' ? 'animate-pulse-glow' : '' }}">
        <svg class="{{ $iconSizes[$size] }} text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/>
        </svg>
    </div>

    @if($withText)
        <span class="{{ $textSizes[$size] }} font-bold tracking-tight">
            Gym<span class="text-gym-primary">ku</span>
        </span>
    @endif
</div>