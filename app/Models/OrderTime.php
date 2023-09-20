<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderTime extends Model
{
    use HasFactory;
    protected $table = 'order_times';
    protected $fillable = ['order_date','order_time','order_id'];
    
  
}
