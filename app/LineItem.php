<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LineItem extends Model
{
    protected $table = 'line_items';

    public function customer() {
        return $this->belongsTo('App\Customer', 'customer_id');
    }
}
