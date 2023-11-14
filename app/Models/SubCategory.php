<?php

namespace App\Models;

use App\Models\Shop;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @method static orderBy(string $string, string $string1)
 * @method static find(mixed $get)
 * @method static where(string $string, $id)
 * @property mixed category_id
 * @property mixed description
 * @property mixed title
 */
class SubCategory extends Model
{
    use HasFactory, HasTranslations;

    // is_approval
    static $PenddingApproval = 0;
    static $AdminApproval = 1;
    static $AdminRefused = -1;
    // is_primary
    static $isPrimary = 1;
    static $isNotPrimary = 0;


    protected $fillable = [
        'title','description', 'price', 'category_id','active','image_url','is_primary','is_approval','quantity','shop_id'
    ];

    public $translatable = ['title','description'];

    public function category(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function carts(){
        return $this->hasMany(Cart::class);
    }

    public static function activateSubCategory($id): bool
    {
        /* $products =  Product::where('sub_category_id',$id)->get();
        foreach ($products as $product) {
            $product->active = true;
            $product->save();
        } */
        $subCategory = SubCategory::find($id);
        $subCategory->active = true;
        $subCategory->save();
        return true;
    }

    public static function disableSubCategory($id): bool
    {
        /* $products =  Product::where('sub_category_id',$id)->get();
        foreach ($products as $product) {
            $product->active = false;
            Cart::where('product_id', '=', $product->id)->where('active', '=', true)->delete();
            $product->save();
        } */
        $subCategory = SubCategory::find($id);
        $subCategory->active = false;
        $subCategory->save();
        return true;
    }

    /* public function shops()
    {
        return $this->belongsToMany(Shop::class, 'shop_sub_category', 'sub_category_id', 'shop_id');
    } */

    public function shops()
    {
        return $this->belongsToMany(Shop::class)
        ->withPivot('price')
        ->withPivot('quantity')
        ->using(ShopSubcategory::class);
    }
    public function deliveryBoys()
    {
        return $this->belongsToMany(DeliveryBoy::class)
        ->withPivot('price')
        ->withPivot('total_quantity')
        ->withPivot('available_quantity')
        ->using(DeliveryBoySubCategory::class);
    }

}
