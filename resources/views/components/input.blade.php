@props([
    'label' => null,
    'name',
    'type' => 'text',
    'icon' => null,
    'error' => null,
    'placeholder' => '',
])

<div class="animate-fade-in-up {{ $attributes->get('class') }}">
    @if($label)
        <label for="{{ $name }}" class="block text-sm font-medium text-gym-text-muted mb-2">
            {{ $label }}
        </label>
    @endif

    <div class="relative">
        @if($icon)
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                {!! $icon !!}
            </div>
        @endif

        <input
            type="{{ $type }}"
            id="{{ $name }}"
            name="{{ $name }}"
            placeholder="{{ $placeholder }}"
            {{ $attributes->except('class')->merge([
                'class' => 'w-full ' . ($icon ? 'pl-12' : 'pl-4') . ' pr-4 py-3 rounded-xl bg-gym-darker border text-gym-text placeholder-gym-text-muted/50 focus:outline-none focus:border-gym-primary focus:ring-1 focus:ring-gym-primary/50 transition-all duration-200 ' . ($error ? 'border-gym-error ring-1 ring-gym-error/50' : 'border-gym-border')
            ]) }}
        >
    </div>

    @if($error)
        <p class="mt-2 text-sm text-gym-error flex items-center gap-1">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01"/>
            </svg>
            {{ $error }}
        </p>
    @endif
</div>