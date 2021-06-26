<?php

namespace App\Models;

use App\Traits\Trash;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use Trash;
    protected $guarded = [];

    public function item()
    {
        return $this->belongsTo('App\Models\Item', 'item_id', 'id');

    }
    public function service()
    {
        return $this->belongsTo('App\Models\Service', 'service_id', 'id');

    }
    public function pdf_path()
    {
        if ($this->invoice_file) {
            return asset('storage/uploads/pdf/' . $this->invoice_file);
        }

        return null;
    }
}
