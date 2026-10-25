<x-filament-panels::page>
<style>
.schedule-shell{display:flex;flex-direction:column;gap:18px}
.schedule-toolbar{background:var(--gray-900,#fff);border:1px solid rgba(128,128,128,.22);border-radius:16px;padding:16px;box-shadow:0 1px 3px rgba(0,0,0,.08)}
.schedule-filters{display:grid;grid-template-columns:180px minmax(240px,1fr) auto;gap:12px;align-items:end}
.schedule-field label{display:block;font-size:12px;font-weight:600;opacity:.65;margin-bottom:6px}
.schedule-field select{width:100%;min-height:42px;border:1px solid rgba(128,128,128,.35);border-radius:10px;padding:0 12px;background:transparent}
.schedule-segment{display:flex;gap:4px;padding:4px;border-radius:11px;background:rgba(128,128,128,.12)}
.schedule-segment button{border:0;border-radius:8px;padding:8px 14px;cursor:pointer;background:transparent}
.schedule-segment button.active{background:#2563eb;color:#fff}
.schedule-nav{display:grid;grid-template-columns:44px 1fr auto 44px;gap:8px;align-items:center;margin-top:14px}
.schedule-nav button{min-height:40px;border:1px solid rgba(128,128,128,.25);border-radius:10px;padding:0 12px;background:transparent;cursor:pointer}
.schedule-date{text-align:center;font-size:17px;font-weight:700}
.schedule-weekbar{display:grid;grid-template-columns:repeat(7,1fr);gap:8px}
.schedule-daybtn{border:1px solid rgba(128,128,128,.22);border-radius:12px;padding:9px 5px;text-align:center;cursor:pointer;background:rgba(128,128,128,.06)}
.schedule-daybtn.active{background:#2563eb;border-color:#2563eb;color:#fff}
.schedule-daybtn .dow{font-size:11px;text-transform:uppercase;opacity:.75}.schedule-daybtn .num{font-size:19px;font-weight:800;margin-top:2px}
.schedule-grid{display:flex;flex-direction:column;gap:12px}.schedule-day-section{border:1px solid rgba(128,128,128,.2);border-radius:14px;overflow:hidden;background:rgba(128,128,128,.035)}
.schedule-column{width:100%}
.schedule-column-head{padding:11px 12px;border-bottom:1px solid rgba(128,128,128,.18);font-weight:700;text-transform:capitalize}
.schedule-lessons{padding:10px;display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:10px}
.lesson-card{border-left:4px solid #2563eb;border-radius:10px;padding:10px;background:rgba(128,128,128,.08)}
.lesson-top{display:flex;justify-content:space-between;gap:8px;align-items:flex-start}.lesson-pair{font-size:12px;font-weight:800;color:#2563eb}.lesson-time{font-size:12px;opacity:.7;white-space:nowrap}
.lesson-name{font-weight:700;margin-top:5px;line-height:1.25}.lesson-meta{font-size:12px;opacity:.72;margin-top:5px;line-height:1.45}.lesson-room{font-weight:700}
.lesson-badge{display:inline-block;font-size:10px;font-weight:800;border-radius:999px;padding:2px 7px;margin-top:6px;background:#f59e0b;color:#111827}.lesson-card.cancelled{border-left-color:#ef4444;opacity:.65}.lesson-card.cancelled .lesson-name{text-decoration:line-through}
.schedule-empty{padding:20px 8px;text-align:center;font-size:12px;opacity:.5}
@media(max-width:900px){.schedule-filters{grid-template-columns:1fr}.schedule-nav{grid-template-columns:42px 1fr 42px}.schedule-print{display:none}.schedule-weekbar{overflow-x:auto;grid-template-columns:repeat(7,64px)}.schedule-lessons{grid-template-columns:1fr}.schedule-column.empty{display:none}}
@media print{.schedule-toolbar,.schedule-weekbar,aside,header,.fi-topbar,.fi-sidebar{display:none!important}.schedule-grid{overflow:visible;grid-template-columns:repeat(5,1fr)}.schedule-column{min-width:0}.schedule-column:nth-child(n+6){display:none}.fi-main{max-width:none!important}}
</style>
<div class="schedule-shell">
 <div class="schedule-toolbar">
  <div class="schedule-filters">
   <div class="schedule-field"><label>Показывать</label><select wire:model.live="scope"><option value="group">По группе</option><option value="teacher">По преподавателю</option></select></div>
   @if($scope==='group')
   <div class="schedule-field"><label>Группа</label><select wire:model.live="groupId"><option value="">Все группы</option>@foreach($this->groups as $g)<option value="{{$g->id}}">{{$g->name}}</option>@endforeach</select></div>
   @else
   <div class="schedule-field"><label>Преподаватель</label><select wire:model.live="teacherId"><option value="">Все преподаватели</option>@foreach($this->teachers as $t)<option value="{{$t->id}}">{{$t->full_name}}</option>@endforeach</select></div>
   @endif
   <div class="schedule-segment"><button wire:click="$set('mode','day')" class="{{$mode==='day'?'active':''}}">День</button><button wire:click="$set('mode','week')" class="{{$mode==='week'?'active':''}}">Неделя</button></div>
  </div>
  <div class="schedule-nav"><button wire:click="previous">‹</button><button wire:click="today" class="schedule-date">{{$this->selectedDateLabel}}</button><button type="button" onclick="window.print()" class="schedule-print">Печать</button><button wire:click="next">›</button></div>
 </div>

 <div class="schedule-weekbar">
 @foreach($this->days as $day)<button wire:click="selectDate('{{$day->toDateString()}}')" class="schedule-daybtn {{$day->toDateString()===$date?'active':''}}"><div class="dow">{{$day->translatedFormat('D')}}</div><div class="num">{{$day->format('d')}}</div></button>@endforeach
 </div>

 <div class="schedule-grid">
 @foreach($this->days as $day)
 @php($lessons=$this->lessonsFor($day->toDateString()))
 <section class="schedule-day-section schedule-column {{$lessons->isEmpty()?'empty':''}}">
  <div class="schedule-column-head">{{$day->translatedFormat('D, d F')}}</div>
  <div class="schedule-lessons">
   @forelse($lessons as $lesson)
   <article class="lesson-card {{$lesson->change_type==='cancelled'?'cancelled':''}}">
    <div class="lesson-top"><div class="lesson-pair">{{$lesson->pair_number ? $lesson->pair_number.' пара' : 'Занятие'}}</div><div class="lesson-time">{{substr($lesson->starts_at,0,5)}}–{{substr($lesson->ends_at,0,5)}}</div></div>
    <div class="lesson-name">{{$lesson->discipline?->name}}</div>
    <div class="lesson-meta">
     @if($scope!=='group')<div>{{$lesson->group?->name}}</div>@endif
     @if($scope!=='teacher')<div>{{$lesson->teacher?->full_name}}</div>@endif
     @if($lesson->room)<div class="lesson-room">каб. {{$lesson->room}}</div>@endif
     @if($lesson->lesson_type)<div>{{$lesson->lesson_type}}</div>@endif
     @if($lesson->subgroup)<div>{{$lesson->subgroup}}</div>@endif
    </div>
    @if($lesson->change_type)<span class="lesson-badge">{{['cancelled'=>'Отменено','replacement'=>'Замена','moved'=>'Перенос'][$lesson->change_type]??'Изменено'}}</span>@endif
    @if($lesson->change_note)<div class="lesson-meta">{{$lesson->change_note}}</div>@endif
   </article>
   @empty<div class="schedule-empty">Занятий нет</div>@endforelse
  </div>
 </section>
 @endforeach
 </div>
</div>
</x-filament-panels::page>
