<?php

namespace App\Models;
use App\Models\Shop;
use App\Models\DeliveryBoy;
use App\Models\CouponDeliveryBoy;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property mixed expired_at
 * @property mixed offer
 * @property mixed description
 * @property mixed code
 * @property mixed started_at
 * @property mixed max_discount
 * @property mixed min_order
 * @property false|mixed for_only_one_time
 * @property false|mixed for_new_user
 * @method static orderBy(string $string, string $string1)
 * @method static find($id)
 */
class Coupon extends Model
{
    use hasFactory, HasTranslations;

    protected $fillable = [
        'category_id','code','description','is_activate','offer','started_at','expired_at','type','min_order','max_discount',
        'for_only_one_time','for_new_user','is_active','is_primary','is_approval'
    ];       
    
    public $translatable = ['description']; 
    public function category(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
    
    public function deliveryBoys()
    {
        return $this->belongsToMany(DeliveryBoy::class)
        ->using(CouponDeliveryBoy::class);
    }
    public function shops()
    {
        return $this->belongsToMany(Shop::class)
        ->using(CouponDeliveryBoy::class);
    }
}
