<?php

namespace App\Models;

use App\Traits\Trash;
use Illuminate\Database\Eloquent\Model;

class Payslip extends Model
{
    use Trash;

    protected $guarded = [];

    public function image_path()
    {
        if ($this->payslip_image) {
            return asset('storage/uploads/payslip/' . $this->payslip_image);
        }
        return null;
    }
}
