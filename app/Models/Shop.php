<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @method static find($id)
 * @method static doesnthave(string $string)
 * @method static has(string $string)
 * @property mixed delivery_fee
 * @property mixed default_tax
 * @property mixed longitude
 * @property mixed latitude
 * @property mixed address
 * @property mixed description
 * @property mixed mobile
 * @property mixed email
 * @property mixed name
 * @property false|mixed open
 * @property false|mixed available_for_delivery
 * @property false|mixed|string image_url
 * @property mixed delivery_range
 * @property mixed minimum_delivery_charge
 * @property mixed delivery_cost_multiplier
 */
class Shop extends Model
{

    use hasFactory,HasTranslations;


    protected $fillable = [
        'name','manager_id','image_url','email','mobile','barcode','latitude',
        'longitude','address','rating','delivery_range','total_rating','category_id',
        'default_tax', 'available_for_delivery', 'open','distance'
    ];
    public $translatable = ['name'];


    public function manager(){
        return $this->belongsTo(Manager::class);
    }

    public function shopReviews(){
        return $this->hasMany(ShopReview::class);
    }

   public function orders(){
        return $this->hasMany(Order::class);
    }
    public function transactions(){
        return $this->hasMany(Transaction::class);
    }
        public static function total_shop_to_admin($id){
        $shop=Shop::find($id);
        return $shop->transactions->sum('total');
    }
    public static function orders_total($id){
         $shop=Shop::find($id);
        return $shop->orders->sum('total');
    }



    public function category(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public static function updateShopImage(Request  $request,$id){
        $shop=Shop::find($id);
        $old_image = $shop->image_url;
        $path = $request->file('image')->store('shop_images', 'public');
        $shop->image_url=$path;
        $shop->save();
        Storage::disk('public')->delete($old_image);
    }


    public static function updateShopImageWithApi(Request  $request,$id){
        $shop=Shop::find($id);
        $old_image = $shop->image_url;
        $url = "shop_images/".Str::random(10).".jpg";
        $data = base64_decode($request->image);
        Storage::disk('public')->put($url, $data);
        $shop->image_url=$url;
        Storage::disk('public')->delete($old_image);
        return $shop->save();
    }

    public static function deleteImage($id): bool
    {
        $productImage = ProductImage::find($id);
        if($productImage){
            Storage::disk('public')->delete($productImage->url);
            return $productImage->delete();
        }
        return false;
    }

    static function generateGoogleMapLocationUrl( $latitude,  $longitude){
        return "http://maps.google.com/maps?q=$latitude+$longitude";
    }

    public function subCategory()
    {
        return $this->belongsToMany(SubCategory::class)
        ->withPivot('price')
        ->withPivot('quantity')
        ->using(ShopSubcategory::class);
    }

    public function ShopSubcategory(){
        return $this->belongsToMany(ShopSubcategory::class);
    }

    public function calculateDistance($userLatitude, $userLongitude) {
       $earthRadius = 6371; // نصف قطر الأرض بالكيلومتر
        $latDiff = deg2rad($userLatitude - $this->latitude);
        $lonDiff = deg2rad($userLongitude - $this->longitude);
        $a = sin($latDiff / 2) * sin($latDiff / 2) + cos(deg2rad($this->latitude)) * cos(deg2rad($userLatitude)) * sin($lonDiff / 2) * sin($lonDiff / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distance = $earthRadius * $c;

        $this->distance = $distance;
        $this->save();

        return $distance;
    }
}
