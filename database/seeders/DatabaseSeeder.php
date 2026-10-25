<?php
namespace Database\Seeders;
use App\Models\User;
use Illuminate\Database\Seeder;
class DatabaseSeeder extends Seeder {
 public function run(): void {
  User::updateOrCreate(['email'=>'admin@chistop.local'], ['name'=>'Администратор','password'=>'ChangeMe123!','role'=>'admin','is_active'=>true]);
 }
}
