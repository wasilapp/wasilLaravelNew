<?php

namespace App\Http\Controllers\Admin;

use App\Models\Shop;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use App\Http\Trait\UploadImage;
use App\Http\Trait\MessageTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\SubCategoryRequest;
use Illuminate\Support\Facades\Validator;

class SubCategoryController extends Controller
{
    use UploadImage;
    use MessageTrait;

    private $SubCategory;
    private $Category;
    private $shop;
    public function __construct(SubCategory $SubCategory,Category $Category,Shop $shop)
    {
        $this->SubCategory = $SubCategory;
        $this->Category = $Category;
        $this->shop = $shop;
    }

    public function index()
    {
        $subCategories = $this->SubCategory->with('category')->orderBy('updated_at', 'DESC')
        ->where('is_approval', 1)
        ->where('is_primary', 1)
        ->paginate(10);
        return view('admin.sub-categories.sub-categories')->with([
            'sub_categories' => $subCategories
        ]);
    }

    public function getSubCategoryShop()
    {
        $subCategories = $this->SubCategory->with('category')->orderBy('updated_at', 'DESC')
        ->where('is_approval', 1)
        ->where('is_primary', 0)
        ->paginate(10);
        // dd($subCategories);
        return view('admin.sub-categories.sub-categories-shop')->with([
            'sub_categories' => $subCategories
        ]);
    }

    public function SubCategoriesRequest(){
        $subCategories = $this->SubCategory->with(['category'])->orderBy('updated_at', 'DESC')->where('is_approval', "=", 0)->paginate(10);
        foreach ($subCategories as $subCategory) {
            $shopIds = $subCategory->shops()->pluck('shops.id');
            $shops = Shop::whereIn('id', $shopIds)->get();
            $subCategory->shops = $shops;
        }

        return view('admin.sub-categories.sub-categories-requests')->with([
            'sub_categories' => $subCategories
        ]);
    }
    public function showSubCategoriesRequest($id)
    {
        try {
            $subCategory = $this->SubCategory->with('category')->find($id);
            return view('admin.sub-categories.sub-categories-requests-show')->with([
                'sub_category' => $subCategory
            ]);
        }catch(\Exception $e){
            Log::info($e->getMessage());
            DB::rollBack();
            //  return $e->getMessage();
            return route('admin.sub-categories-requests.index');
        }

    }


    public function create()
    {
        $shops= $this->shop->all();
        $categories= $this->Category->all();

        return view('admin.sub-categories.create-sub-category',compact('shops', 'categories'));
    }


    public function store(SubCategoryRequest $request)
    {
        try {
            DB::beginTransaction ();
            //dd($request->all());
            if ($request->has('image')) {
                $path  =  $this->upload($request->image,'sub_categories');
            }
            $data = [
                'price' => $request->input ('price'),
                'quantity' => $request->input ('quantity'),
                'image_url' => $path,
                'category_id' => $request->input ('category'),
                'active' => 1,
                "is_approval" => 1,
                'title' => [
                    'en' => $request->input('title')['en'],
                    'ar' => $request->input('title')['ar']
                ],
                'description' => [
                    'en' => $request->input('description')['en'],
                    'ar' => $request->input('description')['ar']
                ],
            ];
            if (isset($request->is_primary)){
                $data['is_primary'] = 1;
            }
            if (isset($request->shop_id)) {
                $data['shop_id'] = $request->shop_id;
            }

            $this->SubCategory->create($data);
            DB::commit();
            return redirect()->route('admin.sub-categories.index')->with('success','Sub category added successfully');
        }catch(\Exception $e){
            Log::info($e->getMessage());
            DB::rollBack();
            return redirect()->route('admin.sub-categories.create')->with(['error' => 'Something wrong']);
        }

    }

    public function shopstoresubcategories(SubCategoryRequest $request)
    {
        try {
            
            DB::beginTransaction ();
            $shop = auth()->user()->shop;
            $shop = $this->shop->find($request->shop_id);
            if ($request->has('image')) {
                $path  =  $this->upload($request->image,'sub_categories');
            }
            $data = [
                'shop_id' => $request->input ('shop_id'),
                'price' => $request->input ('price'),
                'quantity' => $request->input ('quantity'),
                'image_url' => $path,
                'category_id' => $request->input ('category'),
                'active' => 1,
                "is_approval" => 1,
                "is_primary" => 0,
                'title' => [
                    'en' => $request->input('title')['en'],
                    'ar' => $request->input('title')['ar']
                ],
                'description' => [
                    'en' => $request->input('description')['en'],
                    'ar' => $request->input('description')['ar']
                ],
            ];

            $subCategory = $this->SubCategory->create($data);
           // dd($subCategory);
            if ($subCategory) {
                $sub_category_id = $subCategory->id;
                $shop->subCategory()->syncWithoutDetaching([ $sub_category_id => ['price' => $request->input ('price'),'quantity' => $request->input ('quantity')]]);
            }
            DB::commit();
            return $this->returnDataMessage('data', ['subCategory'=>$subCategory],trans('message.Sub category added successfully'));
        }catch(\Exception $e){
            Log::info($e->getMessage());
            DB::rollBack();
            return response(['errors' => [$e->getMessage()]], 402);
        }

    }

    public function show($id)
    {
    }


    public function edit($id)
    {
        $subCategory = $this->SubCategory->with('category')->find($id);
        return view('admin.sub-categories.edit-sub-category')->with([
            'sub_category' => $subCategory
        ]);

    }



    public function update(SubCategoryRequest $request,$id)
    {

        try {
            $subCategory = $this->SubCategory->find($id);
            $subCategory = $this->SubCategory->findOrfail($id);
            DB::beginTransaction ();

           $data = [
                'price' => $request->input ('price'),
                'quantity' => $request->input ('quantity'),
                'title' => [
                    'en' => $request->input('title')['en'],
                    'ar' => $request->input('title')['ar']
                ],
                'description' => [
                    'en' => $request->input('description')['en'],
                    'ar' => $request->input('description')['ar']
                ],
            ];

            if (isset($request->is_primary)) {
                $data['is_primary'] = 1;
            }
            if (isset($request->image)) {
                $image_url = $this->updateImage($subCategory->image_url,$request->image,'sub_categories');
                $data['image_url']= $image_url;
            }
            $subCategory->update($data);
            DB::commit();
            return redirect()->route('admin.sub-categories.index')->with('success','Sub category updated successfully');
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            DB::rollBack();
            return redirect()->route('admin.sub-categories.edit',['id'=>$id])->with(['error' => 'Something wrong']);
        }

    }


    public function destroy($id){
        try {
            $SubCategory = $this->SubCategory->findOrFail($id);
            DB::beginTransaction();
            $SubCategory->delete();
            DB::commit();
            return redirect()->route('admin.sub-categories.index')->with('success','Sub category deleted successfully');
        }catch(\Exception $e){
            DB::rollBack();
            return redirect()->route('admin.sub-categories.index')->with(['error' => 'Sub category not deleted']);
        }
    }

    public function accept(Request $request, $id)
    {
        $SubCategory = $this->SubCategory->findOrFail($id);
        $SubCategory->update(['is_approval' => 1]);
        return redirect()->route('admin.sub-categories-requests.index')->with('success','Successfully approved');
    }

    public function accept2(Request $request, $id)
    {
        $SubCategory = $this->SubCategory->findOrFail($id);
        $SubCategory->update(['is_approval' => 1]);
        return response()->json(['success' =>  $SubCategory]);    
    }
    public function decline2(Request $request, $id)
    {
        $SubCategory = $this->SubCategory->findOrFail($id);
        $SubCategory->update(['is_approval' => -1]);
        return response()->json(['success' =>  $SubCategory]);    
    }

    public function decline(Request $request, $id)
    {
        $SubCategory = $this->SubCategory->findOrFail($id);
        $SubCategory->update(['is_approval' => -1]);
        return redirect()->route('admin.sub-categories-requests.index')->with('success','Rejected successfully');
    }
}
