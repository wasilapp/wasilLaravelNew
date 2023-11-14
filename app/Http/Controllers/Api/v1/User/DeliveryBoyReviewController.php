<?php

namespace App\Http\Controllers\Api\v1\User;

use App\Models\User;
use App\Models\Order;
use App\Models\Category;
use App\Rules\RatingRule;
use App\Models\DeliveryBoy;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use App\Http\Trait\MessageTrait;
use App\Models\DeliveryBoyReview;
use App\Http\Controllers\Controller;
use App\Http\Controllers\FCMController;

class DeliveryBoyReviewController extends Controller
{

    use MessageTrait;
    private $deliveryBoy;
    private $category;
    private $subCategory;
    public function __construct(DeliveryBoy $deliveryBoy,Category $category,SubCategory $subCategory)
    {
        $this->deliveryBoy = $deliveryBoy;
        $this->category = $category;
        $this->subCategory = $subCategory;
    }
    public function deliveryBoyLocation(Request $request){
        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');

        $query = DeliveryBoy::query();

        if (!empty($request->shop_id)) {
            $query->where('shop_id', $request->shop_id);
        }

        $query->where('category_id', $category_id);

        if ($latitude && $longitude) {
            $query->selectRaw(
                '*, (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance',
                [$latitude, $longitude, $latitude]
            );

            $query->whereRaw('distance <= delivery_boys.distance');
            $query->orderByRaw('distance ASC');
        }

        if ($request->input('search')) {
            $query->where('name', 'LIKE', '%' . $request->input('search') . '%');
        }

        $deliveryBoy = $query->get();

        if (!$deliveryBoy->isEmpty()) {
            return $this->returnData('data', ['deliveryBoy'=>$deliveryBoy]);
        } else {
            return $this->errorResponse(trans('message.any-delivery-boy-yet'),200);
        }
    }
    
    public function deliveryBoynearsetLocation(Request $request){
        $latitude = $request->latitude;
        $longitude = $request->longitude;
        $min_price = $request->min_price;
        $max_price = $request->max_price;

        if($request->category_id == 1){
            $category = $this->category->find($request->category_id);
            $subCategory = $this->subCategory->find($request->sub_category_id);
            $shops = $subCategory->shops;
          
            $shops = $subCategory->shops->filter(function ($shop) use ($max_price, $min_price) {
                return  $shop->pivot->price >= $min_price &&  $shop->pivot->price <= $max_price;
            });
            return $shops;
            $nearestDeliveryBoy = [];
            $max_distance = $request->max_distance;

            foreach($shops as $shop){
                $shopDeliveryBoys = $shop->deliveryBoys->filter(function ($deliveryBoy) use ($category,$latitude,$longitude,$max_distance){
                    $deliveryBoy->distance = $this->deliveryBoy->haversine($latitude, $longitude, $deliveryBoy->latitude, $deliveryBoy->longitude);

                    return $deliveryBoy->category_id == $category->id && $deliveryBoy->is_offline == 0 && $deliveryBoy->is_approval == 1 && $deliveryBoy->distance <= $max_distance;
                });
                foreach($shopDeliveryBoys as $shopDeliveryBoy){
                    array_push($nearestDeliveryBoy,$shopDeliveryBoy);
                }
            }

            if ($nearestDeliveryBoy) {
                return $this->returnData('data', ['nearestDeliveryBoy'=>$nearestDeliveryBoy]);
            } else {
                return $this->errorResponse(trans('message.any-delivery-boy-yet'),200);
            }
        } else {
            $category = $this->category->find($request->category_id);
            $deliveryBoys = 
            Deliveryboy::whereHas('category', function ($query) use ($category) {
                $query->where('id', $category->id);
            })
            ->where('is_approval', 1)
            ->where('is_offline', 0)->get();
            
            foreach ($deliveryBoys as $deliveryBoy) {
                $deliveryBoy->distance = $this->deliveryBoy->haversine($latitude, $longitude, $deliveryBoy->latitude, $deliveryBoy->longitude);
            }
            
            $max_distance = $request->max_distance;
          // return  gettype($deliveryBoys);
            $nearestDeliveryBoy = $deliveryBoys->sortBy('distance')->filter(function ($deliveryBoy) use ($max_distance) {
                return $deliveryBoy->distance <= $max_distance;
            });

            // return $nearestDeliveryBoy;

            if (!$nearestDeliveryBoy->isEmpty()) {
                return $this->returnData('data', ['nearestDeliveryBoy'=>$nearestDeliveryBoy]);
            } else {
                return $this->errorResponse(trans('message.any-delivery-boy-yet'),200);
            }
        }
    
    }

    public function deliveryBoySearch(Request $request){
        $name = $request->input('name');
        $agencyName = $request->input('agency_name');

        $deliveryBoy = DeliveryBoy::query();

        if ($name) {
            $deliveryBoy->where('name', 'LIKE', '%' . $name . '%');
        }

        if ($agencyName) {
            $deliveryBoy->where('agency_name', 'LIKE', '%' . $agencyName . '%');
        }

        $results = $deliveryBoy->get();

        if ($results->isEmpty()) {
            return $this->returnData('data', ['deliveryBoy' => $results]);
        } else {
            return $this->errorResponse(trans('message.any-delivery-boy-yet'), 200);
        }
    }

    public function store(Request $request)
    {

        $this->validate($request, [
            'order_id' => 'required',
            'rating' => [
                'required',
                new RatingRule()
            ],
        ]);

        $user_id = auth()->user()->id;
        $order = Order::find($request->order_id);

        if ($order) {
            if(!$order->delivery_boy_id)
                return response(['errors' => ['This order haven\'t any delivery boy']], 403);
            $deliveryBoyReview = DeliveryBoyReview::where('order_id', '=', $order->id)->get();
            if ($deliveryBoyReview->isEmpty()) {
                return response(['errors' => ['This delivery boy is already reviewed']], 403);
            }

            $deliveryBoy = DeliveryBoy::find($order->delivery_boy_id);
            $total_rating = $deliveryBoy->total_rating;
            $deliveryBoy->rating = ($deliveryBoy->rating * $total_rating + $request->rating) / ($total_rating + 1);
            $deliveryBoy->total_rating = $total_rating+1;

            $deliveryBoyReview = new DeliveryBoyReview();
            $deliveryBoyReview->rating = $request->rating;
            $deliveryBoyReview->review = $request->review;
            $deliveryBoyReview->user_id = $user_id;
            $deliveryBoyReview->order_id = $request->order_id;
            $deliveryBoyReview->delivery_boy_id = $order->delivery_boy_id;
            if($deliveryBoyReview->save() && $deliveryBoy->save()) {
                FCMController::sendMessage("Rating", "You have a new rating added",$deliveryBoy->fcm_token);
                return response(['message' => ['This delivery boy is been reviewed']], 200);
            }
        }else {
            return response(['errors' => ['This order is not available']], 403);
        }
        return response(['message' => ['There is something wrong']], 403);
    }


}
