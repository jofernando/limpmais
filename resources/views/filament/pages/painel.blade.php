<x-filament::page>
    <x-filament::form wire:submit.prevent="submit">
        {{ $this->form }}
        
        <x-filament::button type="submit" size="sm">
            Filtrar
        </x-filament::button>
    </x-filament::form>

    @isset($stats)
        <div class="grid gap-4 lg:gap-8 md:grid-cols-3">
            @foreach ($stats as $stat)
                <x-filament::stats.card :value="$stat['valor']" :label="$stat['titulo']" />
            @endforeach
        </div>
    @endisset
</x-filament::page>