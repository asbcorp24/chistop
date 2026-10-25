<x-filament-panels::page>
    <div class="mx-auto max-w-3xl space-y-6">
        <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <h2 class="text-lg font-semibold">Синхронизация из XML</h2>
            <p class="mt-2 text-sm text-gray-500">Загрузите XML расписания. Перед импортом файл проверяется, вычисляется SHA-256 и создаётся запись в истории импорта.</p>

            <form wire:submit="import" class="mt-6 space-y-5">
                <div>
                    <label class="mb-1 block text-sm font-medium">Организация</label>
                    <select wire:model="organizationId" class="w-full rounded-xl border-gray-300 dark:bg-gray-950">
                        @foreach($this->organizations as $organization)
                            <option value="{{ $organization->id }}">{{ $organization->name }}</option>
                        @endforeach
                    </select>
                    @error('organizationId') <div class="mt-1 text-sm text-danger-600">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium">XML-файл</label>
                    <input type="file" wire:model="xmlFile" accept=".xml,text/xml,application/xml" class="block w-full rounded-xl border border-gray-300 p-3 dark:border-gray-700">
                    @error('xmlFile') <div class="mt-1 text-sm text-danger-600">{{ $message }}</div> @enderror
                    <div wire:loading wire:target="xmlFile" class="mt-2 text-sm text-primary-600">Загрузка файла…</div>
                </div>

                <x-filament::button type="submit" icon="heroicon-o-arrow-up-tray" wire:loading.attr="disabled">
                    Загрузить и проверить XML
                </x-filament::button>
            </form>
        </div>
    </div>
</x-filament-panels::page>
