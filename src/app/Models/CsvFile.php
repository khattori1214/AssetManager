<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CsvFile extends Model
{
   $csvData = CsvFile::orderByDesc('generated_at')->get();
}
