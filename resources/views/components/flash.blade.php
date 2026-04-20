@props(['type' => 'success', 'message'])

@php
$configs = [
    'success' => [
        'bg' => 'bg-gym-success/10',
        'border' => 'border-gym-success/30',
        'text' => 'text-gym-success',
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>',
    ],
    'error' => [
        'bg' => 'bg-gym-error/10',
        'border' => 'border-gym-error/30',
        'text' => 'text-gym-error',
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
    ],
];

$config = $configs[$type];
@endphp

<div id="flash-{{ $type }}" class="fixed top-4 right-4 z-50 animate-fade-in-up">
    <div class="flex items-center gap-3 px-5 py-3 rounded-xl {{ $config['bg'] }} border {{ $config['border'] }} {{ $config['text'] }} backdrop-blur-sm">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            {!! $config['icon'] !!}
        </svg>
        <span class="text-sm font-medium">{{ $message }}</span>
        <button onclick="this.closest('#flash-{{ $type }}').remove()" class="ml-2 hover:opacity-70 transition-opacity">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>
</div>

<script>
    setTimeout(() => {
        const flash = document.getElementById('flash-{{ $type }}');
        if (flash) {
            flash.style.transition = 'opacity 0.5s ease-out';
            flash.style.opacity = '0';
            setTimeout(() => flash.remove(), 500);
        }
    }, 5000);
</script>