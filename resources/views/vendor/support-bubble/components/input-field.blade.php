@php
$inputClasses = config('support-bubble.classes.input');
@endphp

<label for="support-bubble-{{ $name }}" class="font-medium text-sm {{ $hidden ?? false ? 'hidden' : '' }}">

    <div class="{{ config('support-bubble.classes.label') }}">
        {!! $label !!}
    </div>

    @if(($type ?? null) === 'textarea')
        <textarea
            name="{{ $name }}"
            id="support-bubble-{{ $name }}"
            rows="4"
            required
            class="{{ $inputClasses }}"
        >{{ $value ?? '' }}</textarea>
    @else
        <input
            type="{{ $type ?? 'text' }}"
            name="{{ $name }}"
            id="support-bubble-{{ $name }}"
            required
            value="{{ $value ?? '' }}"
            class="{{ $inputClasses }}"
        >
    @endif
</label>
