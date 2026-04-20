@props([
    'variant' => 'primary', // primary, secondary, danger
    'size' => 'md', // sm, md, lg
    'type' => 'button',
    'href' => null,
])

@php
$baseClasses = 'inline-flex items-center justify-center gap-2 font-semibold rounded-xl transition-all duration-200 cursor-pointer focus:outline-none';

$variantClasses = [
    'primary' => 'text-white bg-gradient-to-r from-gym-primary to-amber-500 hover:from-gym-primary-dark hover:to-amber-600 shadow-lg shadow-gym-primary/25 hover:shadow-gym-primary/40 focus:ring-2 focus:ring-gym-primary/50 transform hover:scale-[1.02] active:scale-[0.98]',
    'secondary' => 'text-gym-text-muted hover:text-gym-error bg-gym-card hover:bg-gym-error/10 border border-gym-border hover:border-gym-error/30',
    'danger' => 'text-white bg-gym-error hover:bg-red-600 shadow-lg shadow-gym-error/25 hover:shadow-gym-error/40',
];

$sizeClasses = [
    'sm' => 'px-3 py-2 text-sm',
    'md' => 'px-4 py-3 text-base',
    'lg' => 'px-6 py-4 text-lg',
];

$classes = $baseClasses . ' ' . $variantClasses[$variant] . ' ' . $sizeClasses[$size];
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif