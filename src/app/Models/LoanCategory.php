<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanCategory extends Model
{
    protected $table = 'loan_categories';

    protected $primaryKey = 'category_id';

    protected $fillable = [
        'category_id',
        'category_name',
        'max_loan_days',
        'generated_at',
    ];
}
