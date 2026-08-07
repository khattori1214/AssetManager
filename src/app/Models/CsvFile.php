<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class CsvFile extends Model
{
   const CREATED_AT = null;

   protected $table = 'csv_files';

   protected $primaryKey = 'csv_file_id';

   protected $fillable = [
      'file_name',
      'target_period_start',
      'record_count',
      'generated_at',
   ];
   // 経理連携用CSV出力バッチ処理
   public function csvData()
   {
      return CsvFile::orderByDesc('generated_at')->get();
   }
}