<?php
namespace App\Services;
use App\Models\AcademicDebt;
use App\Models\AssessmentSheet;
use Illuminate\Support\Facades\DB;
class AssessmentSheetService {
 public function close(AssessmentSheet $sheet, ?int $userId): void {
  DB::transaction(function() use($sheet,$userId){
   $sheet->load('rows');
   foreach($sheet->rows as $row){
    $failed=in_array(mb_strtolower((string)$row->result),['не зачтено','незачет','незачёт','неявка'],true)||in_array((string)$row->grade,['1','2'],true);
    if($failed){
     AcademicDebt::firstOrCreate(['student_id'=>$row->student_id,'discipline_id'=>$sheet->discipline_id,'academic_year'=>$sheet->academic_year,'semester'=>$sheet->semester,'reason'=>'Неудовлетворительный результат: '.$this->controlLabel($sheet->control_type)],['status'=>'open']);
    }
   }
   $sheet->update(['status'=>'closed','closed_at'=>now(),'closed_by'=>$userId]);
  });
 }
 private function controlLabel(string $type): string{return ['credit'=>'зачёт','differentiated_credit'=>'дифференцированный зачёт','exam'=>'экзамен','coursework'=>'курсовая работа','practice'=>'практика'][$type]??$type;}
}
