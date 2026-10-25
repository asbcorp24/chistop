<x-filament-panels::page>
    <div class="grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2 rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <h2 class="text-lg font-semibold">Синхронизация из XML расписания</h2>
            <p class="mt-2 text-sm text-gray-500">
                Импортируются специальности, преподаватели, дисциплины, кабинеты, группы, виды занятий, нагрузка и расписание.
                Повторная загрузка того же файла не создаёт дубли.
            </p>

            <div class="mt-6 space-y-5">
                <div>
                    <label class="mb-1 block text-sm font-medium">Организация</label>
                    <select wire:model="organizationId" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-950">
                        @foreach($this->organizations as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                    @error('organizationId') <p class="mt-1 text-sm text-danger-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium">XML-файл</label>
                    <input type="file" wire:model="xmlFile" accept=".xml,text/xml,application/xml"
                           class="block w-full rounded-lg border border-gray-300 p-3 text-sm dark:border-gray-700">
                    @error('xmlFile') <p class="mt-1 text-sm text-danger-600">{{ $message }}</p> @enderror
                    <p wire:loading wire:target="xmlFile" class="mt-2 text-sm text-primary-600">Загрузка файла…</p>
                </div>

                <x-filament::button wire:click="import" wire:loading.attr="disabled" wire:target="import,xmlFile" icon="heroicon-o-arrow-up-tray">
                    Импортировать и синхронизировать
                </x-filament::button>
            </div>
        </div>

        <div class="rounded-xl bg-primary-50 p-6 ring-1 ring-primary-200 dark:bg-primary-950/30 dark:ring-primary-900">
            <h3 class="font-semibold">Что делает импорт</h3>
            <div class="mt-3 space-y-2 text-sm">
                <p>• Связывает записи по ID из XML.</p>
                <p>• Обновляет существующие записи без дублей.</p>
                <p>• Разворачивает day/hour + период в реальные даты.</p>
                <p>• Сохраняет исходные параметры нагрузки.</p>
                <p>• Пишет ошибки и статистику в историю импорта.</p>
            </div>
        </div>
    </div>

    @if($lastResult)
        <div class="mt-6 rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <h3 class="font-semibold">Последний результат — пакет #{{ $lastResult['id'] }}</h3>
            <div class="mt-4 grid grid-cols-2 gap-3 md:grid-cols-5">
                <div><div class="text-xs text-gray-500">Статус</div><div class="font-semibold">{{ $lastResult['status'] }}</div></div>
                <div><div class="text-xs text-gray-500">Создано</div><div class="font-semibold">{{ $lastResult['created'] }}</div></div>
                <div><div class="text-xs text-gray-500">Обновлено</div><div class="font-semibold">{{ $lastResult['updated'] }}</div></div>
                <div><div class="text-xs text-gray-500">Пропущено</div><div class="font-semibold">{{ $lastResult['skipped'] }}</div></div>
                <div><div class="text-xs text-gray-500">Ошибок</div><div class="font-semibold">{{ $lastResult['errors'] }}</div></div>
            </div>
        </div>
    @endif
</x-filament-panels::page>
