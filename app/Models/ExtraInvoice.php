<?php

namespace App\Models;

use App\Traits\Trash;
use Illuminate\Database\Eloquent\Model;

class ExtraInvoice extends Model
{
    use Trash;
    protected $guarded = [];

    public function booking()
    {
        return $this->belongsTo('App\Models\Booking', 'booking_id', 'id');

    }

    public function pdf_path()
    {
        if($this->invoice_file){
             return asset('storage/uploads/pdf/' . $this->invoice_file);
        }
        return null;
    }
}
