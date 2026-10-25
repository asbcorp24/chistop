<x-filament-panels::page>
<div class="space-y-4">
 <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
  <div class="flex flex-wrap items-end gap-3">
   <div><div class="mb-1 text-xs font-medium text-gray-500">Показывать</div><select wire:model.live="scope" class="rounded-xl"><option value="group">По группе</option><option value="teacher">По преподавателю</option></select></div>
   @if($scope==='group')
    <div class="min-w-56 flex-1"><div class="mb-1 text-xs font-medium text-gray-500">Группа</div><select wire:model.live="groupId" class="w-full rounded-xl"><option value="">Все группы</option>@foreach($this->groups as $g)<option value="{{$g->id}}">{{$g->name}}</option>@endforeach</select></div>
   @else
    <div class="min-w-64 flex-1"><div class="mb-1 text-xs font-medium text-gray-500">Преподаватель</div><select wire:model.live="teacherId" class="w-full rounded-xl"><option value="">Все преподаватели</option>@foreach($this->teachers as $t)<option value="{{$t->id}}">{{$t->full_name}}</option>@endforeach</select></div>
   @endif
   <div class="flex rounded-xl bg-gray-100 p-1 dark:bg-gray-800"><button wire:click="$set('mode','day')" class="rounded-lg px-3 py-2 text-sm {{$mode==='day'?'bg-white shadow dark:bg-gray-700':''}}">День</button><button wire:click="$set('mode','week')" class="rounded-lg px-3 py-2 text-sm {{$mode==='week'?'bg-white shadow dark:bg-gray-700':''}}">Неделя</button></div>
  </div>
  <div class="mt-4 flex items-center justify-between gap-2">
   <x-filament::button color="gray" wire:click="previous" icon="heroicon-m-chevron-left"/>
   <button wire:click="today" class="font-semibold">{{ $this->selectedDateLabel }}</button>
   <div class="flex gap-2"><button type="button" onclick="window.print()" class="hidden rounded-lg px-3 py-2 text-sm font-medium ring-1 ring-gray-300 md:block print:hidden">Печать</button><x-filament::button color="gray" wire:click="next" icon="heroicon-m-chevron-right"/></div>
  </div>
 </div>
 <div class="grid grid-cols-7 gap-2 overflow-x-auto">
 @foreach($this->days as $day)
  <button wire:click="selectDate('{{$day->toDateString()}}')" class="min-w-24 rounded-xl p-2 text-center {{$day->toDateString()===$date?'bg-primary-600 text-white':'bg-white dark:bg-gray-900'}}"><div class="text-xs">{{$day->translatedFormat('D')}}</div><div class="text-lg font-bold">{{$day->format('d')}}</div></button>
 @endforeach
 </div>
 @foreach($this->days as $day)
 @php($lessons=$this->lessonsFor($day->toDateString()))
 <section class="{{$mode==='week'&&!$lessons->count()?'hidden md:block':''}}">
  <h3 class="mb-2 font-semibold">{{$day->translatedFormat('l, d F')}}</h3>
  <div class="space-y-2">
   @forelse($lessons as $lesson)
   <div class="grid grid-cols-[92px_1fr] gap-3 rounded-xl bg-white p-3 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 md:grid-cols-[100px_1.5fr_1fr_1fr_100px]">
    <div>@if($lesson->pair_number)<div class="text-xs font-semibold text-primary-600">{{$lesson->pair_number}} пара</div>@endif<div class="font-bold">{{substr($lesson->starts_at,0,5)}}</div><div class="text-xs text-gray-500">{{substr($lesson->ends_at,0,5)}}</div></div>
    <div><div class="flex flex-wrap items-center gap-2"><div class="font-semibold {{$lesson->change_type==='cancelled'?'line-through opacity-60':''}}">{{$lesson->discipline?->name}}</div>@if($lesson->change_type)<span class="rounded-full px-2 py-0.5 text-xs font-semibold {{$lesson->change_type==='cancelled'?'bg-danger-50 text-danger-700':($lesson->change_type==='replacement'?'bg-warning-50 text-warning-700':'bg-info-50 text-info-700')}}">{{['cancelled'=>'Отменено','replacement'=>'Замена','moved'=>'Перенос'][$lesson->change_type]??'Изменено'}}</span>@endif</div><div class="text-xs text-gray-500 md:hidden">{{$lesson->group?->name}} · {{$lesson->teacher?->full_name}}</div>@if($lesson->subgroup)<span class="text-xs text-primary-600">{{$lesson->subgroup}}</span>@endif @if($lesson->change_note)<div class="mt-1 text-xs font-medium text-warning-700">{{$lesson->change_note}}</div>@endif</div>
    <div class="hidden md:block">{{$lesson->group?->name}}</div><div class="hidden md:block">{{$lesson->teacher?->full_name}}</div>
    <div><div class="font-medium">{{$lesson->room}}</div><div class="text-xs text-gray-500">{{$lesson->lesson_type}}</div></div>
   </div>
   @empty<div class="rounded-xl border border-dashed p-5 text-center text-sm text-gray-500">Занятий нет</div>@endforelse
  </div>
 </section>
 @endforeach
</div>
<style>@media print{aside,header,.fi-topbar,.fi-sidebar,.fi-header-actions{display:none!important}main{padding:0!important}.fi-main{max-width:none!important}.shadow-sm{box-shadow:none!important}}</style>
</x-filament-panels::page>
