<x-filament::page>
    <x-filament::form wire:submit.prevent="submit">
        {{ $this->form }}
        
        <x-filament::button type="submit" size="sm">
            Filtrar
        </x-filament::button>
    </x-filament::form>

    @isset($stats)
    <div class="filament-stats grid gap-4 lg:gap-8 md:grid-cols-3">
        @foreach ($stats as $stat)
            <div class="filament-stats-card relative rounded-2xl bg-white p-6 shadow filament-stats-overview-widget-card">
                <div class="space-y-2">
                    <div class="flex items-center space-x-2 text-sm font-medium text-gray-500 rtl:space-x-reverse">
                        <span>{{ $stat['titulo'] }}</span>
                    </div>
                    <div class="text-3xl">
                        {{ $stat['valor'] }}
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    @endisset
</x-filament::page>