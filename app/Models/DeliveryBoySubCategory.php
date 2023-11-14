<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class DeliveryBoySubCategory extends Pivot
{
    use HasFactory;

    protected $table = 'delivery_boy_sub_category';
    protected $fillable = ['delivery_boy_id', 'sub_category_id','price','total_quantity','available_quantity'];

}
