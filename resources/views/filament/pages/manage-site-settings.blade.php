<x-filament-panels::page>
    <x-filament-panels::form wire:submit="save">
        {{ $this->form }}

        <div class="flex items-center gap-3 mt-6">
            <x-filament::button type="submit" color="primary" icon="heroicon-m-check">
                Save All Settings
            </x-filament::button>
        </div>
    </x-filament-panels::form>
</x-filament-panels::page>
