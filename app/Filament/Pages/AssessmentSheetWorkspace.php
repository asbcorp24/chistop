<?php
namespace App\Filament\Pages;
use App\Models\AssessmentSheet;
use App\Models\AssessmentSheetRow;
use App\Services\AssessmentSheetService;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
class AssessmentSheetWorkspace extends Page {
 protected static string|\BackedEnum|null $navigationIcon='heroicon-o-clipboard-document-check';
 protected static ?string $navigationLabel='Ввод ведомости';
 protected static string|\UnitEnum|null $navigationGroup='Контроль успеваемости';
 protected static ?string $title='Массовый ввод ведомости';
 protected string $view='filament.pages.assessment-sheet-workspace';
 public ?int $sheetId=null; public array $rows=[];
 public function mount(): void {$this->sheetId=AssessmentSheet::query()->whereIn('status',['draft','open'])->latest('assessment_date')->value('id');$this->loadSheet();}
 public function updatedSheetId(): void {$this->loadSheet();}
 public function getSheetsProperty(){return AssessmentSheet::with(['group','discipline'])->latest('assessment_date')->get();}
 public function getSheetProperty(){return $this->sheetId?AssessmentSheet::with(['group','discipline','teacher'])->find($this->sheetId):null;}
 public function loadSheet(): void {$this->rows=[];if(!$this->sheetId)return;foreach(AssessmentSheetRow::with('student')->where('assessment_sheet_id',$this->sheetId)->get()->sortBy(fn($r)=>$r->student?->full_name) as $r){$this->rows[$r->id]=['student'=>$r->student?->full_name,'result'=>$r->result,'grade'=>$r->grade,'comment'=>$r->comment];}}
 public function save(): void {$sheet=$this->sheet;if(!$sheet||$sheet->status==='closed')return;foreach($this->rows as $id=>$data){AssessmentSheetRow::whereKey($id)->where('assessment_sheet_id',$sheet->id)->update(['result'=>$data['result']?:null,'grade'=>$data['grade']?:null,'comment'=>$data['comment']?:null]);}$sheet->update(['status'=>'open']);Notification::make()->title('Ведомость сохранена')->success()->send();}
 public function closeSheet(): void {$sheet=$this->sheet;if(!$sheet||$sheet->status==='closed')return;$this->save();app(AssessmentSheetService::class)->close($sheet,auth()->id());Notification::make()->title('Ведомость закрыта. Задолженности сформированы.')->success()->send();}
}
