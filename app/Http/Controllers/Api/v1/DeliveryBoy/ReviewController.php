<?php

namespace App\Http\Controllers\Api\v1\DeliveryBoy;

use App\Http\Controllers\Controller;
use App\Http\Trait\MessageTrait;
use App\Models\DeliveryBoy;
use App\Models\Manager;
use App\Models\Order;
use App\Models\Review;
use App\Models\Shop;
use App\Models\User;
use App\Rules\RatingRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    use MessageTrait;
    private $user;
    private $review;
    private $deliveryBoy;
    private $order;
    private $manager;

    public function __construct(User $user,Review $review,DeliveryBoy $deliveryBoy,Order $order,Manager $manager)
    {
        $this->user = $user;
        $this->review = $review;
        $this->deliveryBoy = $deliveryBoy;
        $this->order = $order;
        $this->manager = $manager;
    }

    public function getRatingBy($status){
        $driver_id = auth()->user()->id;
        $status = strtolower($status);
        switch($status) {
            case('user'):
                $rating = $this->review
                        ->with('user')
                        ->where('rating_to_type', 'driver')
                        ->where('rating_form_type', 'user')
                        ->where('rating_to_id', $driver_id)
                        ->get();
            break;
            case('shop'):
                $rating = $this->review
                        ->with('shop')
                        ->where('rating_to_type', 'driver')
                        ->where('rating_form_type', 'shop')
                        ->where('rating_to_id', $driver_id)
                        ->get();
            break;
            default:
                $rating = $this->review
                        ->with('user','shop')
                        ->where('rating_to_id', $driver_id)
                        ->get();
        }
        $deliveryBoy = $this->deliveryBoy->find($driver_id);
        if ($rating->isNotEmpty())  {
            return $this->returnData('data', ['rating'=>$rating]);
        } else {
            return $this->returnDataMessage('data', ['rating'=>$rating,'deliveryBoy'=>$deliveryBoy], trans('message.any-rating-yet'));
        }
    }

    public function ratingUser(Request $request){
        try {
            $validator = Validator::make($request->all(),[
                'user_id' => 'required',
                'rating' => [
                    'required',
                    new RatingRule()
                ],
            ]);
            if ($validator->fails())
            {
                return $this->errorResponse($validator->errors()->all(), 422);
            }
            DB::beginTransaction ();
            $driver_id = auth()->user()->id;
            $user = $this->user->find($request->user_id);
            if ($user){
                $data = [
                    'rating' => $request->get('rating'),
                    'review' => $request->get('review'),
                    'rating_form_type' => 'driver',
                    'rating_form_id' => $driver_id,
                    'rating_to_type' => 'user',
                    'rating_to_id' => $request->get('user_id')
                ];
                DB::commit();
                $rating = $this->review->create($data);
                return $this->returnDataMessage('data', ['rating'=>$rating], trans('message.review_successfully'));
            }else {
                return $this->errorResponse(trans('message.user_avaliable'), 403);
            }
        }catch(\Exception $e){
            Log::info($e->getMessage());
            DB::rollBack();
            return $this->returnError('400', $e->getMessage());
        }
    }

    public function ratingOrder(Request $request){
        try {
            $validator = Validator::make($request->all(),[
                'order_id' => 'required',
                'rating' => [
                    'required',
                    new RatingRule()
                ],
            ]);
            if ($validator->fails())
            {
                return $this->errorResponse($validator->errors()->all(), 422);
            }
            DB::beginTransaction ();
            $driver_id = auth()->user()->id;
            $order = $this->order->find($request->order_id);
            if ($order){
                $data = [
                    'rating' => $request->get('rating'),
                    'review' => $request->get('review'),
                    'rating_form_type' => 'driver',
                    'rating_form_id' => $driver_id,
                    'rating_to_type' => 'order',
                    'rating_to_id' => $request->get('order_id')
                ];
                DB::commit();
                $rating = $this->review->create($data);
                return $this->returnDataMessage('data', ['rating'=>$rating], trans('message.review_successfully'));
            }else {
                return $this->errorResponse(trans('message.order_avaliable'), 403);
            }
        }catch(\Exception $e){
            Log::info($e->getMessage());
            DB::rollBack();
            return $this->returnError('400', $e->getMessage());
        }
    }

    public function ratingShop(Request $request){
        try {
            $validator = Validator::make($request->all(),[
                'shop_id' => 'required',
                'rating' => [
                    'required',
                    new RatingRule()
                ],
            ]);
            if ($validator->fails())
            {
                return $this->errorResponse($validator->errors()->all(), 422);
            }
            DB::beginTransaction ();
            $driver_id = auth()->user()->id;
            $shop = $this->manager->find($request->shop_id);
            if ($shop){
                $data = [
                    'rating' => $request->get('rating'),
                    'review' => $request->get('review'),
                    'rating_form_type' => 'driver',
                    'rating_form_id' => $driver_id,
                    'rating_to_type' => 'shop',
                    'rating_to_id' => $request->get('shop_id')
                ];
                DB::commit();
                $rating = $this->review->create($data);
                return $this->returnDataMessage('data', ['rating'=>$rating], trans('message.review_successfully'));
            }else {
                return $this->errorResponse(trans('message.shop_avaliable'), 403);
            }
        }catch(\Exception $e){
            Log::info($e->getMessage());
            DB::rollBack();
            return $this->returnError('400', $e->getMessage());
        }
    }
}
