<?php

namespace App\Http\Controllers\Api\v1\User;

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
        $user_id = auth()->user()->id;
        $status = strtolower($status);
        switch($status) {
            case('driver'):
                $rating = $this->review
                        ->with('user','deliveryBoy')
                        ->where('rating_to_type', 'user')
                        ->where('rating_form_type', 'driver')
                        ->where('rating_to_id', $user_id)
                        ->get();
            break;
            case('shop'):
                $rating = $this->review
                        ->with('shop','deliveryBoy')
                        ->where('rating_to_type', 'user')
                        ->where('rating_form_type', 'shop')
                        ->where('rating_to_id', $user_id)
                        ->get();
            break;
            default:
                $rating = $this->review
                        ->with('user','shop','deliveryBoy')
                        ->where('rating_to_id', $user_id)
                        ->get();
        }
        if ($rating->isNotEmpty())  {
            return $this->returnData('data', ['rating'=>$rating]);
        } else {
            return $this->returnDataMessage('data', ['rating'=>$rating], trans('message.any-rating-yet'));
        }
    }

    public function ratingDriver(Request $request){
        try {
            $validator = Validator::make($request->all(),[
                'driver_id' => 'required',
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
            $user_id = auth()->user()->id;
            $driver = $this->deliveryBoy->find($request->driver_id);
            if ($driver){
                $data = [
                    'rating' => $request->get('rating'),
                    'review' => $request->get('review'),
                    'rating_form_type' => 'user',
                    'rating_form_id' => $user_id,
                    'rating_to_type' => 'driver',
                    'rating_to_id' => $request->get('driver_id')
                ];
                DB::commit();
                $rating = $this->review->create($data);
                return $this->returnDataMessage('data', ['rating'=>$rating], trans('message.review_successfully'));
            }else {
                return $this->errorResponse(trans('message.driver_avaliable'), 403);
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
            $user_id = auth()->user()->id;
            $order = $this->order->find($request->order_id);
            if ($order){
                $data = [
                    'rating' => $request->get('rating'),
                    'review' => $request->get('review'),
                    'rating_form_type' => 'user',
                    'rating_form_id' => $user_id,
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
            $user_id = auth()->user()->id;
            $shop = $this->manager->find($request->shop_id);
            if ($shop){
                $data = [
                    'rating' => $request->get('rating'),
                    'review' => $request->get('review'),
                    'rating_form_type' => 'user',
                    'rating_form_id' => $user_id,
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
