<x-filament-panels::page>
<div class="space-y-5">
 <div class="grid gap-4 md:grid-cols-2">
  <label class="block"><span class="text-sm font-medium">Группа</span><select wire:model.live="groupId" class="mt-1 w-full rounded-lg border-gray-300 dark:bg-gray-900"><option value="">Выберите группу</option>@foreach($this->groups as $group)<option value="{{ $group->id }}">{{ $group->name }}</option>@endforeach</select></label>
  <label class="block"><span class="text-sm font-medium">Занятие</span><select wire:model.live="lessonId" class="mt-1 w-full rounded-lg border-gray-300 dark:bg-gray-900"><option value="">Выберите занятие</option>@foreach($this->lessons as $lesson)<option value="{{ $lesson->id }}">{{ $lesson->lesson_date->format('d.m.Y') }} · {{ $lesson->starts_at }} · {{ $lesson->discipline?->name }}</option>@endforeach</select></label>
 </div>
 @if($lessonId && count($records))
 <div class="hidden overflow-x-auto rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900 md:block">
  <table class="w-full text-sm"><thead><tr class="bg-gray-50 dark:bg-gray-800"><th class="p-3 text-left">Студент</th><th class="p-3">Посещение</th><th class="p-3">Оценка</th><th class="p-3 text-left">Комментарий</th></tr></thead>
  <tbody>@foreach($records as $id=>$row)<tr class="border-t border-gray-100 dark:border-gray-800"><td class="p-3 font-medium">{{ $row['name'] }}</td><td class="p-2"><select wire:model="records.{{ $id }}.attendance_status" class="rounded-lg"><option value="present">Был</option><option value="absent">Н</option><option value="late">Опоздал</option><option value="excused">УП</option></select></td><td class="p-2"><input wire:model="records.{{ $id }}.grade" class="w-20 rounded-lg" placeholder="—"></td><td class="p-2"><input wire:model="records.{{ $id }}.comment" class="w-full rounded-lg"></td></tr>@endforeach</tbody></table>
 </div>
 <div class="space-y-3 md:hidden">@foreach($records as $id=>$row)<div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900"><div class="mb-3 font-semibold">{{ $row['name'] }}</div><div class="grid grid-cols-2 gap-3"><select wire:model="records.{{ $id }}.attendance_status" class="rounded-lg"><option value="present">Был</option><option value="absent">Н</option><option value="late">Опоздал</option><option value="excused">УП</option></select><input wire:model="records.{{ $id }}.grade" class="rounded-lg" placeholder="Оценка"></div><input wire:model="records.{{ $id }}.comment" class="mt-3 w-full rounded-lg" placeholder="Комментарий"></div>@endforeach</div>
 <x-filament::button wire:click="save" icon="heroicon-o-check">Сохранить журнал</x-filament::button>
 @else <div class="rounded-xl border border-dashed p-8 text-center text-gray-500">Выберите группу и занятие из импортированного расписания.</div>@endif
</div>
</x-filament-panels::page>
