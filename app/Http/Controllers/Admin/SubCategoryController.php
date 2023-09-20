<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SubCategoryRequest;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Http\Trait\UploadImage;

class SubCategoryController extends Controller
{
    use UploadImage;
    private $SubCategory;
    private $Category;
    public function __construct(SubCategory $SubCategory,Category $Category)
    {
        $this->SubCategory = $SubCategory;
        $this->Category = $Category;
    }

    public function index()
    {
        $subCategories = $this->SubCategory->with('category')->orderBy('updated_at', 'DESC')->paginate(10);
        return view('admin.sub-categories.sub-categories')->with([
            'sub_categories' => $subCategories
        ]);
    }

    public function create()
    {
        return view('admin.sub-categories.create-sub-category')->with([
            'categories'=> $this->Category->all()
        ]);
    }


    public function store(SubCategoryRequest $request)
    {
        try {
            DB::beginTransaction ();

            if ($request->has('image')) {
                $path  =  $this->upload($request->image,'sub_categories');
            }
            $data = [
                'price' => $request->input ('price'),
                'image_url' => $path,
                'category_id' => $request->input ('category'),
                'active' => 1,
                'title' => [
                    'en' => $request->input('title')['en'],
                    'ar' => $request->input('title')['ar']
                ],
            ];
            $this->SubCategory->create($data);
            DB::commit();
            return redirect()->route('admin.sub-categories.index')->with('success','Sub category added successfully');
        }catch(\Exception $e){
            Log::info($e->getMessage());
            DB::rollBack();
            return redirect()->route('admin.sub-categories.create')->with(['error' => 'Something wrong']);
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
            if ($request->has('image')) {
               $path  =  $this->upload($request->image,'sub_categories');
            }
           $data = [
                'image_url' => $path,
                'price' => $request->input ('price'),
                'title' => [
                    'en' => $request->input('title')['en'],
                    'ar' => $request->input('title')['ar']
                ],
            ];
            $subCategory->update($data);
            DB::commit();
            return redirect()->route('admin.sub-categories.index')->with('success','Sub category updated successfully');
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            DB::rollBack();
            return redirect()->route('admin.sub-categories.edit')->with(['error' => 'Something wrong']);
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
}
