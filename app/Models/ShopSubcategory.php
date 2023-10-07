<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ShopSubcategory extends Pivot
{
    use HasFactory;

    protected $table = 'shop_sub_category';

    protected $fillable = [
        'sub_category_id','shop_id','price','quantity'
    ];
}
