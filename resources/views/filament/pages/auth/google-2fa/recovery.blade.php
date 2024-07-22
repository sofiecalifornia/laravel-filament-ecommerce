<x-filament-panels::page.simple>

    <x-slot name="subheading">
        {{ trans('or') }}

        {{ $this->otpAction }}
    </x-slot>

    <x-filament-panels::form wire:submit="submit">
        {{ $this->form }}

        <x-filament-panels::form.actions
            :actions="$this->getCachedFormActions()"
            :full-width="$this->hasFullWidthFormActions()"
        />
    </x-filament-panels::form>


</x-filament-panels::page.simple>
