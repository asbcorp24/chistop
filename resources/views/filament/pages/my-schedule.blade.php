<x-filament-panels::page>
<style>
.ms{display:flex;flex-direction:column;gap:16px}.ms-panel,.ms-group{border:1px solid rgba(128,128,128,.2);border-radius:14px;background:rgba(128,128,128,.04);overflow:hidden}.ms-controls{padding:14px;display:flex;gap:14px;align-items:end;justify-content:space-between;flex-wrap:wrap}.ms-left{display:flex;gap:12px;align-items:end;flex-wrap:wrap}.ms label{display:block;font-size:12px;font-weight:700;opacity:.65;margin-bottom:5px}.ms input{border:1px solid rgba(128,128,128,.3);border-radius:9px;padding:8px 10px}.ms-switch{display:flex;background:rgba(128,128,128,.12);padding:3px;border-radius:10px}.ms-switch button,.ms-nav button{border:0;background:transparent;border-radius:8px;padding:8px 13px;cursor:pointer}.ms-switch .on{background:#2563eb;color:white}.ms-nav{display:flex;align-items:center;gap:8px}.ms-nav button{border:1px solid rgba(128,128,128,.25)}.ms-week{display:grid;grid-template-columns:repeat(7,1fr);border-top:1px solid rgba(128,128,128,.18)}.ms-day{padding:10px;text-align:center;border:0;border-right:1px solid rgba(128,128,128,.15);background:transparent;cursor:pointer}.ms-day b{display:block}.ms-group-head{padding:12px 15px;font-weight:800;border-bottom:1px solid rgba(128,128,128,.18)}.ms-day-head{padding:8px 15px;background:rgba(128,128,128,.08);font-weight:700}.ms-pair{display:grid;grid-template-columns:105px 1fr;gap:12px;padding:12px 15px;border-top:1px solid rgba(128,128,128,.12)}.ms-no{display:flex;gap:8px;align-items:center}.ms-num{display:flex;width:38px;height:38px;align-items:center;justify-content:center;border-radius:9px;background:#dbeafe;color:#1d4ed8;font-weight:900}.ms-time{font-size:11px;opacity:.6}.ms-items{display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:8px}.ms-card{border:1px solid rgba(128,128,128,.2);border-radius:10px;padding:10px}.ms-sub{display:inline-block;font-size:11px;border-radius:999px;padding:2px 7px;background:rgba(128,128,128,.12)}.ms-change{background:#fef3c7;color:#92400e}.ms-title{font-weight:800;margin-top:5px}.ms-meta{font-size:12px;opacity:.7;margin-top:4px}.ms-empty{padding:32px;text-align:center;opacity:.6}@media(max-width:700px){.ms-controls,.ms-left{display:block}.ms-left>*{margin-bottom:10px}.ms-week{overflow-x:auto;grid-template-columns:repeat(7,65px)}.ms-pair{grid-template-columns:1fr}.ms-items{grid-template-columns:1fr}.ms-nav{justify-content:space-between}}
</style>
<div class="ms">
 <div class="ms-panel">
  <div class="ms-controls">
   <div class="ms-left">
    <div><label>Дата</label><input type="date" wire:model.live="date"></div>
    <div><label>Показывать</label><div class="ms-switch"><button wire:click="$set('mode','day')" class="{{$mode==='day'?'on':''}}">День</button><button wire:click="$set('mode','week')" class="{{$mode==='week'?'on':''}}">Неделя</button></div></div>
   </div>
   <div class="ms-nav"><button wire:click="previous">←</button><button wire:click="today">Сегодня</button><button wire:click="next">→</button></div>
  </div>
  @if($mode==='week')<div class="ms-week">@foreach($days as $dayDate=>$day)<button wire:click="selectDay('{{$dayDate}}')" class="ms-day"><span>{{$day['short']}}</span><b>{{$day['date']}}</b></button>@endforeach</div>@endif
 </div>
 @forelse($scheduleGroups as $sg)
 <section class="ms-group">
  <div class="ms-group-head">Учебная группа · {{$sg['name']}}</div>
  @foreach($days as $dayDate=>$day)
   @php($items=collect($sg['days'][$dayDate]??[]))
   @if($mode==='week')<div class="ms-day-head">{{$day['name']}} · {{$day['date']}}</div>@endif
   @forelse($items->groupBy('pair_number') as $pairNo=>$pairItems)
    @php($first=$pairItems->first())
    <div class="ms-pair">
     <div class="ms-no"><div class="ms-num">{{$pairNo?:'—'}}</div><div><b>пара</b><div class="ms-time">{{substr($first['starts_at']??'',0,5)}}–{{substr($first['ends_at']??'',0,5)}}</div></div></div>
     <div class="ms-items">
      @foreach($pairItems as $l)<div class="ms-card">
       <div>@if($l['subgroup'])<span class="ms-sub">{{$l['subgroup']}}</span>@endif @if($l['change_type'])<span class="ms-sub ms-change">{{['replacement'=>'Замена','moved'=>'Перенос'][$l['change_type']]??'Изменено'}}</span>@endif</div>
       <div class="ms-title">{{$l['subject_name']}}</div>
       <div class="ms-meta">@if($l['room'])Кабинет <b>{{$l['room']}}</b>@endif @if($l['teacher']) · {{$l['teacher']}}@endif</div>
      </div>@endforeach
     </div>
    </div>
   @empty @if($mode==='week')<div class="ms-empty" style="padding:10px">Занятий нет</div>@endif @endforelse
  @endforeach
 </section>
 @empty <div class="ms-panel ms-empty"><b>На выбранную дату занятий нет</b><div>Или аккаунт ещё не связан с преподавателем/студентом.</div></div>
 @endforelse
</div>
</x-filament-panels::page>
