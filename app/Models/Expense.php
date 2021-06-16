<?php

namespace App\Models;

use App\Traits\Trash;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use Trash;
    protected $guarded = [];

    public function expense_category(){
       return $this->belongsTo(ExpenseCategory::class,'expense_category_id');
    }


    public function expense_type(){
        return $this->belongsTo(ExpenseType::class,'expense_type_id');
     }
}
