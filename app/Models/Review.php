<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    use HasFactory,HasTranslations;

    protected $fillable = [
        'rating','review','rating_form_type','rating_form_id','rating_to_type','rating_to_id'
    ];
    public $translatable = ['review'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class,'rating_form_id');
    }

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class,'rating_form_id');
    }

    public function deliveryBoy(): BelongsTo
    {
        return $this->belongsTo(DeliveryBoy::class,'rating_form_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class,'rating_form_id');
    }

}
