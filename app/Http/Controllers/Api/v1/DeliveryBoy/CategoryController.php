<?php

namespace App\Http\Controllers\Api\v1\DeliveryBoy;

use App\Models\Category;
use App\Models\DeliveryBoy;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use App\Http\Trait\MessageTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    use MessageTrait;
    private $category;
    private $deliveryBoy;
    private $subCategory;

    public function __construct(DeliveryBoy $deliveryBoy,Category $category, SubCategory $subCategory)
    {
        $this->category = $category;
        $this->deliveryBoy = $deliveryBoy;
        $this->subCategory = $subCategory;
    }
    public function index(Request $request)
    {
        $categories = Category::with(['shops' => function ($query) {
                $query->whereHas('manager', function ($managerQuery) {
                    $managerQuery->where('is_approval', 1);
                });
            }])->get();
        if ($categories) {
            return $this->returnData('data', ['categories'=>$categories]);
        } else {
            return $this->errorResponse(trans('message.any-Categories-yet'), 200);
        }
    }

    public function mySubCategories(Request $request)
    {
        $deliveryBoy = $this->deliveryBoy->find(auth()->user()->id);
        $subCategories =  $deliveryBoy->subCategory()->get();
        if (!empty($subCategories)) {
            return $this->returnData('data', ['subCategories'=>$subCategories]);
        } else {
            return $this->errorResponse(trans('message.any-subcategories-yet'), 200);
        }
    }

    public function selectSubCategories(Request $request)
    {
        try {
            DB::beginTransaction ();
            $deliveryBoy = $this->deliveryBoy->find(auth()->user()->id);
            $subCategories = $request->all();
            if (!empty($subCategories)) {
                foreach ($subCategories as $sub){
                    if ($sub['available_quantity'] > $sub['total_quantity']){
                        return $this->errorResponse(trans('message.The available quantity cannot be greater than the total quantity'), 200);
                    }
                    $data = [
                        'total_quantity' => $sub['total_quantity'],
                        'available_quantity' => $sub['available_quantity']
                    ];

                    if (isset($sub['price'])) {
                        $data['price'] = $sub['price'];
                    }

                    $deliveryBoy->subCategory()->syncWithoutDetaching([
                        $sub['sub_category_id'] => $data
                    ]);
                }
            }
            $getSubCategories = $deliveryBoy->subCategory;

            $total_quantity = 0;
            $available_quantity = 0;
            foreach ($getSubCategories as $sub){
                //return $sub->details->total_quantity;
                $total_quantity = $total_quantity + $sub->details->total_quantity;
                $available_quantity = $available_quantity + $sub->details->available_quantity;
            }
            if($total_quantity > $deliveryBoy->total_capacity  || $available_quantity > $deliveryBoy->total_capacity ){
                return $this->errorResponse(trans('message.The available quantity cannot be greater than the total capacity'), 200);

            }
            $data = [
                'total_quantity' => $total_quantity,
                'available_quantity' => $available_quantity
            ];
            if($available_quantity === 0){
                $data['is_offline']= 1;
            }
            $deliveryBoy->update($data);
           // return $available_quantity;
            DB::commit();
            return $this->returnDataMessage('data', ['SubCategories'=>$getSubCategories],trans('message.subCategory-added'));
        }catch(\Exception $e){
            Log::error($e->getMessage());
            DB::rollBack();
            return $this->returnError('400', $e->getMessage());
        }
    }

    public function remove($id){
        try {
            DB::beginTransaction ();
            $deliveryBoy = $this->deliveryBoy->find(auth()->user()->id);
            $subCategory = $this->subCategory->find($id);
            if (!$subCategory) {
                return $this->errorResponse(trans('message.item-not-found'),400);
            }
            if (!$deliveryBoy->subCategory()->where ('id', $subCategory->id)->exists()) {
                return $this->errorResponse(trans('message.item-not-related'),400);
            }

            $deliveryBoy->subCategory()->detach($subCategory);

            $total_quantity = 0;
            $available_quantity = 0;
            $getSubCategories = $deliveryBoy->subCategory;
            foreach ($getSubCategories as $sub){
                //return $sub->details->total_quantity;
                $total_quantity = $total_quantity + $sub->details->total_quantity;
                $available_quantity = $available_quantity + $sub->details->available_quantity;
            }
            $data = [
                'total_quantity' => $total_quantity,
                'available_quantity' => $available_quantity
            ];
            if($available_quantity === 0){
                $data['is_offline']= 1;
            }
            $deliveryBoy->update($data);

            DB::commit();
            return $this->returnMessage(trans('message.item-removed-successfully'),204);
        }catch(\Exception $e){
            Log::info($e->getMessage());
            DB::rollBack();
            return $this->returnError('400', $e->getMessage());
        }
    }
}
