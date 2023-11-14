<?php

namespace App\Models;

use App\Models\Order;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Statu extends Model
{
    use HasFactory,HasTranslations;
    public $timestamps = false;

    protected $fillable = [
        'title'
    ];
    public $translatable = ['title'];


    public function orders(){
        return $this->hasMany(Order::class);
    }
}
