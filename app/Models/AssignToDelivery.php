<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignToDelivery extends Model
{
    use HasFactory;
    protected $guarded = [];
    
      public function orders(){
        return $this->belongsTo(Order::class,'order_id', 'id');
    }
    
}
