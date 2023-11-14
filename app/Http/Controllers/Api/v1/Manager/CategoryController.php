<?php

namespace App\Http\Controllers\Api\v1\Manager;

use App\Models\Shop;
use App\Models\Admin;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use App\Http\Trait\UploadImage;
use App\Http\Trait\MessageTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Notification;
use App\Http\Requests\Api\Manager\SubCategoryRequest;
use App\Notifications\SubCategoryAddedByShopNotification;
use App\Notifications\Fairbase\SubCategoryAddedByShopNotificationFcm;

class CategoryController extends Controller
{
    use MessageTrait;
    use UploadImage;
    use SubCategoryAddedByShopNotificationFcm;

    private $category;
    private $subCategory;
    private $shop;
    public function __construct(Category $category, SubCategory $subCategory, Shop $shop)
    {
        $this->category = $category;
        $this->subCategory = $subCategory;
        $this->shop = $shop;
    }
    public function index(Request $request)
    {

        $allCategories = $this->category::with('subCategories')->get();
        $shop = auth()->user()->shop;
        $subCategoriesShop = $shop->subCategory;
        $result = [];
        foreach ($allCategories as $category) {
            //return  $category;
            $categoryData = $category->toArray();
           // return  $categoryData;
            $categoryData['sub_shop_categories'] = $subCategoriesShop->where('category_id', $category->id)->all();

            $subAdminCategories = $category->subAdminCategories;
            $allSubAdminCategoriesResult = [];
            foreach ($subAdminCategories as $cat) {
                $subAdminCategoriesResult = $cat->toArray();
                if($shop->subCategory()->where('sub_category_id', $cat->id)->exists()){
                    $subAdminCategoriesResult['status'] = 'added';
                } else {
                    $subAdminCategoriesResult['status'] = 'notAdded';
                }
                array_push($allSubAdminCategoriesResult,$subAdminCategoriesResult );
            }

            $categoryData['subAdminCategories']=$allSubAdminCategoriesResult;
            $result[] = $categoryData;
        }
        if ($result) {
            return $this->returnData('data', ['allCategories'=>$result]);
        } else {
            return $this->errorResponse(trans('message.any-Categories-yet'), 200);
        }
    }
    /* public function index2(Request $request)
    {

        $allCategories = $this->category::with('subCategories','subAdminCategories')->get();
        $shop = auth()->user()->shop;
        $subCategoriesShop = $shop->subCategory;
        $result = [];
        foreach ($allCategories as $category) {
            $categoryData = $category->toArray();
            $categoryData['sub_shop_categories'] = $subCategoriesShop->where('category_id', $category->id)->all();
            $result[] = $categoryData;
        }
        if ($result) {
            return $this->returnData('data', ['allCategories'=>$result]);
        } else {
            return $this->errorResponse(trans('message.any-Categories-yet'), 200);
        }
    } */

    public function mainCategories(Request $request)
    {
        $mainCategories = $this->category::all();
        if ($mainCategories) {
            return $this->returnData('data', ['mainCategories'=>$mainCategories]);
        } else {
            return $this->errorResponse(trans('message.any-mainCategories-yet'), 200);
        }
    }

    public function mySubCategories(Request $request)
    {
        $shop = auth()->user()->shop;
        $category_id =  $shop->category->id;
        $category =  $shop->category->title;

        $subCategoriesPending = $shop->subCategory()
            ->where('is_approval', 0)
            ->get();
        $subCategoriesAccepted = $shop->subCategory()
            ->where('is_approval', 1)
            ->get();
        $subCategories = $shop->subCategory()
            ->where('is_approval', '!=' , -1)
            ->get();
        if ($subCategories) {
            return $this->returnData('data',
             ['id' => $category_id,
             'categoryName' => $category,
             'subCategoriesPending'=>$subCategoriesPending,
             'subCategoriesAccepted'=>$subCategoriesAccepted,
             'subCategories'=>$subCategories,
            ]);
        } else {
            return $this->errorResponse(trans('message.any-subCategories-yet'), 200);
        }
    }

    public function mySubCategoriesShow($id){
        try {
            DB::beginTransaction ();
            $shop = auth()->user()->shop;
            $subCategory = $shop->subCategory()->find($id);
            DB::commit();
            return $this->returnData('data', ['subCategory'=>$subCategory]);
        }catch(\Exception $e){
            Log::error($e->getMessage());
            DB::rollBack();
            return $this->returnError('400', $e->getMessage());
        }

    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(),[
                'title.en' => 'required|unique:sub_categories,title->en',
                'title.ar' => 'required|unique:sub_categories,title->ar',
                'image_url' => 'required',
                'price' => 'required',
            ]);

            if ($validator->fails())
            {
                return $this->errorResponse($validator->errors()->all(), 422);
            }
            DB::beginTransaction ();
            $shop = auth()->user()->shop;
            if ($request->has('image_url')) {
                $path  =  $this->upload($request->image_url,'sub_categories');
            }
            $data = [
                'price' => $request->input ('price'),
                'image_url' => $path,
                'category_id' => $shop->category_id,
                'active' => 1,
                'title' => [
                    'en' => $request->input('title')['en'],
                    'ar' => $request->input('title')['ar']
                ],
                'description' => [
                    'en' => $request->input('description')['en'],
                    'ar' => $request->input('description')['ar']
                ],
                'is_primary'=>0,
                'shop_id'=> $shop->id
            ];
            $subCategory = $this->subCategory->create($data);
            if ($subCategory) {
                $sub_category_id = $subCategory->id;
                $shop->subCategory()->syncWithoutDetaching([ $sub_category_id => ['price' => $request->input ('price'),'quantity' => $request->input ('quantity')]]);
            }
            $admins = Admin::all();
            Notification::send($admins,new SubCategoryAddedByShopNotification($subCategory));
            $this->sendSubCategoryAddedByShopNotificationFcm($subCategory);

            DB::commit();
            return $this->returnDataMessage('data', ['subCategory'=>$subCategory],trans('message.item-created-Please-wait-admin-approval'));
        }catch(\Exception $e){
            Log::error($e->getMessage());
            DB::rollBack();
            return $this->returnError('400', $e->getMessage());
        }

    }

    public function update(Request $request,$id)
    {
        try {

            DB::beginTransaction ();
            $shop = auth()->user()->shop;
            $subCategory = $this->subCategory->findOrFail($id);
            if (!$subCategory) {
                return $this->errorResponse(trans('message.sub-category-not-found'),400);
            }
            $data = [
                'is_approval' => 0,
            ];
            if ($request->title['en']) {
                $data['title']['en'] = $request->title['en'];
            }
            if ($request->title['ar']) {
                $data['title']['ar'] = $request->title['ar'];
            }
            // if ($request->description['en']) {
            //     $data['description']['en'] = $request->description['en'];
            // }
            // if ($request->description['ar']) {
            //     $data['description']['ar'] = $request->description['ar'];
            // }
            if ($request->image_url) {
                $path  =  $this->upload($request->image_url,'sub_categories');
                $data['image_url'] = $path;
            }
            $subCategory = $this->subCategory->update($data);
            if ($subCategory) {
                $sub_category_id = $subCategory->id;
                $shop->subCategory()->syncWithoutDetaching($sub_category_id);
            }
            DB::commit();
            return $this->returnDataMessage('data', ['subCategory'=>$subCategory],trans('message.item-update-Please-wait-admin-approval'));
        }catch(\Exception $e){
            Log::error($e->getMessage());
            DB::rollBack();
            return $this->returnError('400', $e->getMessage());
        }
    }

    public function selectSubCategories(Request $request)
    {
        //return $request->all();
        try {

            DB::beginTransaction ();
            $shop = auth()->user()->shop;
            //$subCategories = json_decode($request->subCategories, true);
            $subCategories = $request->all();
            if (!empty($subCategories)) {
                foreach ($subCategories as $sub){
                    //return $sub['sub_category_id'];
                   // $shop->subCategory()->syncWithoutDetaching($sub);

                    $shop->subCategory()->syncWithoutDetaching([
                        $sub['sub_category_id'] => [
                            'price' => $sub['price'],
                            'quantity' => $sub['quantity']
                            ]
                    ]);

                }
            }
            $getSubCategories = $shop->subCategory;
            DB::commit();
            return $this->returnDataMessage('data', ['SubCategories'=>$getSubCategories],trans('message.subCategory-added'));
        }catch(\Exception $e){
            Log::error($e->getMessage());
            DB::rollBack();
            return $this->returnError('400', $e->getMessage());
        }
    }
    /* public function destroy($id){
    try {
        DB::beginTransaction ();
        $user = auth()->user();

        $subCategory = $this->subCategory->findOrFail($id);
        if (!$subCategory) {
            return $this->errorResponse(trans('message.item-not-found'),400);
        }
        if ($subCategory->is_primary == 1) {
            return $this->errorResponse(trans('message.service-primary-cannot-delete'), 403);
        }
        $subCategory->delete();
        DB::commit();
        return $this->returnMessage(trans('message.subCategory-deleted-successfully'),204);
    }catch(\Exception $e){
        Log::info($e->getMessage());
        DB::rollBack();
        return response(['errors' => [$e->getMessage()]], 402);
    } */
    public function remove($id){
        try {
            DB::beginTransaction ();
            $user = auth()->user();
            $shop = auth()->user()->shop;
            $subCategory = $this->subCategory->find($id);
            if (!$subCategory) {
                return $this->errorResponse(trans('message.item-not-found'),400);
            }
            if (!$shop->subCategory()->where ('id', $subCategory->id)->exists()) {
                return $this->errorResponse(trans('message.item-not-related'),400);
            }

            $shop->subCategory()->detach($subCategory);

            if ($subCategory->shop_id === $shop->id){
              
                $subCategory->update([
                    'active' => 0
                ]);
            }
            DB::commit();
            return $this->returnMessage(trans('message.item-removed-successfully'),204);
        }catch(\Exception $e){
            Log::info($e->getMessage());
            DB::rollBack();
            return $this->returnError('400', $e->getMessage());
        }
    }
    public function showOrHide($id, $statu){
        try {
            DB::beginTransaction ();
            $user = auth()->user();
            $shop = auth()->user()->shop;
            $subCategory = $this->subCategory->find($id);
            if (!$subCategory) {
                return $this->errorResponse(trans('message.item-not-found'),400);
            }
            if (!$shop->subCategory()->where ('id', $subCategory->id)->exists()) {
                return $this->errorResponse(trans('message.item-not-related'),400);
            }

            $shop->subCategory()->updateExistingPivot($subCategory->id, [
                'is_show' => $statu
            ]);
            
            
            if($statu === "show"){
                if($subCategory->shop_id === $shop->id ){
                    $subCategory->update([
                        'active' => 1
                    ]);
                } 
                DB::commit();
                return $this->returnMessage(trans('message.The item was displayed successfully'),204);
            } 
            if($statu === "hidden"){
                if($subCategory->shop_id === $shop->id ){
                    $subCategory->update([
                        'active' => 0
                    ]);
                } 
                DB::commit();
                return $this->returnMessage(trans('message.The item was successfully hidden'),204);
            } 
            
        }catch(\Exception $e){
            Log::info($e->getMessage());
            DB::rollBack();
            return $this->returnError('400', $e->getMessage());
        }
    }
}
