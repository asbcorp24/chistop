<x-filament-panels::page>
<style>
.tw{display:flex;flex-direction:column;gap:16px}.tw-card{border:1px solid rgba(128,128,128,.2);border-radius:14px;background:rgba(128,128,128,.04);overflow:hidden}.tw-head{padding:16px;display:flex;justify-content:space-between;gap:16px;align-items:end;flex-wrap:wrap}.tw-select label{display:block;font-size:12px;font-weight:700;opacity:.6;margin-bottom:5px}.tw-select select{min-width:280px;border:1px solid rgba(128,128,128,.3);border-radius:9px;padding:8px}.tw-name{font-size:20px;font-weight:800}.tw-kpis{display:grid;grid-template-columns:repeat(6,1fr);gap:10px}.tw-kpi{padding:14px}.tw-kpi small{display:block;opacity:.6}.tw-kpi b{display:block;font-size:24px;margin-top:4px}.tw-title{padding:12px 15px;font-weight:800;border-bottom:1px solid rgba(128,128,128,.16)}.tw-row{display:flex;justify-content:space-between;gap:15px;padding:11px 15px;border-top:1px solid rgba(128,128,128,.12)}.tw-row:first-child{border-top:0}.tw-muted{font-size:12px;opacity:.65}.tw-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px}.tw-table{width:100%;border-collapse:collapse}.tw-table th,.tw-table td{text-align:left;padding:10px 12px;border-top:1px solid rgba(128,128,128,.13)}.tw-table th{font-size:11px;text-transform:uppercase;opacity:.6}.tw-warn{color:#b45309}.tw-ok{color:#15803d}.tw-empty{padding:18px;opacity:.6}@media(max-width:1100px){.tw-kpis{grid-template-columns:repeat(3,1fr)}}@media(max-width:700px){.tw-kpis{grid-template-columns:repeat(2,1fr)}.tw-grid{grid-template-columns:1fr}.tw-select select{min-width:0;width:100%}.tw-row{display:block}.tw-table thead{display:none}.tw-table tr{display:block;padding:8px;border-top:1px solid rgba(128,128,128,.13)}.tw-table td{display:flex;justify-content:space-between;border:0;padding:3px 6px}}
</style>
<div class="tw">
 <div class="tw-card tw-head">
  <div><div class="tw-muted">Рабочее пространство</div><div class="tw-name">{{$this->teacher?->full_name??'Преподаватель не выбран'}}</div></div>
  @if(!auth()->user()?->teacher_id)<div class="tw-select"><label>Преподаватель</label><select wire:model.live="teacherId"><option value="">Выберите</option>@foreach($this->teachers as $t)<option value="{{$t->id}}">{{$t->full_name}}</option>@endforeach</select></div>@endif
 </div>
 @if($teacherId)
 @php($s=$this->stats)
 <div class="tw-kpis">
  @foreach([['Нагрузка',$s['fact'].' / '.($s['plan']?:'—')],['Средний балл',$s['avg']?:'—'],['Посещаемость',$s['attendance'].'%'],['Без темы',$s['missing_topics']],['Незакрытый журнал',$s['open_journal']],['Сегодня',$this->todayLessons->count()]] as $x)
   <div class="tw-card tw-kpi"><small>{{$x[0]}}</small><b>{{$x[1]}}</b></div>
  @endforeach
 </div>
 <div class="tw-grid">
  <section class="tw-card"><div class="tw-title">Сегодня</div>
   @forelse($this->todayLessons as $l)<div class="tw-row"><div><b>{{$l->pair_number}} пара · {{$l->group?->name}}</b><div class="tw-muted">{{$l->discipline?->name}} @if($l->room)· каб. {{$l->room}}@endif</div></div><div>{{substr($l->starts_at,0,5)}}</div></div>@empty<div class="tw-empty">Сегодня занятий нет.</div>@endforelse
  </section>
  <section class="tw-card"><div class="tw-title">Нужно закрыть</div>
   @forelse($this->problems as $e)<div class="tw-row"><div><b>{{$e->lesson?->group?->name}} · {{$e->lesson?->discipline?->name}}</b><div class="tw-muted">{{$e->lesson?->lesson_date?->format('d.m.Y')}} · {{$e->lesson?->pair_number}} пара</div></div><div class="tw-warn">@if(!$e->topic)Нет темы @endif @if(!$e->is_closed)· Журнал открыт@endif</div></div>@empty<div class="tw-empty tw-ok">Просроченных записей нет.</div>@endforelse
  </section>
 </div>
 <section class="tw-card"><div class="tw-title">Выполнение нагрузки</div>
  <table class="tw-table"><thead><tr><th>Группа</th><th>Дисциплина</th><th>Учебный год</th><th>План</th><th>Факт</th><th>%</th></tr></thead><tbody>
   @forelse($this->loads as $l)<tr><td>{{$l->group?->name??'—'}}</td><td>{{$l->discipline?->name}}</td><td>{{$l->academic_year}}</td><td>{{$l->hours}}</td><td>{{$l->fact}}</td><td><b>{{$l->pct!==null?$l->pct.'%':'—'}}</b></td></tr>@empty<tr><td colspan="6" class="tw-empty">Нагрузка появится после синхронизации Rector XML.</td></tr>@endforelse
  </tbody></table>
 </section>
 <div class="tw-muted">Показатели предназначены для самоконтроля и управления учебным процессом и не являются автоматической оценкой качества работы преподавателя.</div>
 @endif
</div>
</x-filament-panels::page>
