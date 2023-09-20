<?php

namespace App\Models;

use App\Helpers\AppSetting;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class Privacy extends Model
{
    use HasFactory;


   protected $fillable = [
        'title',
    ];
}
