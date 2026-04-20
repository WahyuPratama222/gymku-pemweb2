@props([
    'title',
    'value' => '—',
    'icon',
    'iconBg' => 'gym-primary',
    'delay' => '100',
])

@php
$iconBgClasses = [
    'gym-primary' => 'bg-gym-primary/10 text-gym-primary',
    'amber' => 'bg-amber-500/10 text-amber-500',
    'success' => 'bg-gym-success/10 text-gym-success',
    'error' => 'bg-gym-error/10 text-gym-error',
];

$borderColors = [
    'gym-primary' => 'hover:border-gym-primary/30',
    'amber' => 'hover:border-amber-500/30',
    'success' => 'hover:border-gym-success/30',
    'error' => 'hover:border-gym-error/30',
];
@endphp

<div class="bg-gym-card border border-gym-border rounded-2xl p-6 {{ $borderColors[$iconBg] }} transition-all duration-300 animate-fade-in-up animation-delay-{{ $delay }}">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm text-gym-text-muted">{{ $title }}</p>
            <p class="text-3xl font-bold mt-1">{{ $value }}</p>
        </div>
        <div class="w-12 h-12 rounded-xl {{ $iconBgClasses[$iconBg] }} flex items-center justify-center">
            {!! $icon !!}
        </div>
    </div>
</div>