<?php

namespace App\Http\Controllers\Api\v1\User;

use App\Http\Controllers\Controller;
use App\Http\Controllers\FCMController;
use App\Http\Trait\MessageTrait;
use App\Models\DeliveryBoy;
use App\Models\DeliveryBoyReview;
use App\Models\Order;
use App\Rules\RatingRule;
use Illuminate\Http\Request;

class DeliveryBoyReviewController extends Controller
{

    use MessageTrait;

    public function deliveryBoyLocation(Request $request,$category_id){
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
