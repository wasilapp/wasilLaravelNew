<?php

namespace App\Http\Controllers\Admin;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Trait\UploadImage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;

class CategoryController extends Controller
{
    use UploadImage;
    private $category;
    public function __construct(Category $category)
    {
        $this->category = $category;
    }

    public function index()
    {
        $categories = $this->category->orderBy('updated_at', 'DESC')->paginate(10);
        return view('admin.categories.categories')->with([
            'categories' => $categories
        ]);
    }

    public function create()
    {
        return view('admin.categories.create-category');
    }

    public function store(CategoryRequest $request)
    {
        try {
            DB::beginTransaction ();
            if ($request->has('image')) {
                $path  =  $this->upload($request->image,'categories-icons');
            }
            $data = [
                'image_url' => $path,
                'commesion' => $request->input ('commesion'),
                'delivery_fee' => $request->input ('delivery_fee'),
                'type' => $request->input ('type'),
                'active' => 1,
                'title' => [
                    'en' => $request->input('title')['en'],
                    'ar' => $request->input('title')['ar']
                ],
            ];
            $this->category->create($data);
            DB::commit();
            return redirect()->route('admin.categories.index')->with('success','Category added successfully');
        }catch(\Exception $e){
            Log::info($e->getMessage());
            DB::rollBack();
            //  return $e->getMessage();
            return redirect()->route('admin.categories.create')->with(['error' => 'Something wrong']);
        }
    }

    public function show($id)
    {
    }


    public function edit($id)
    {
        $category = $this->category->find($id);
        return view('admin.categories.edit-category')->with([
            'category' => $category
        ]);

    }


    public function update(CategoryRequest $request, $id)
    {
        try {
            if (isset($request->active)) {
                $this->category->activateCategory($id);
                $active = 1;
            }else{
                $this->category->disableCategory($id);
                $active = 0;
            }

            $category = $this->category->findOrfail($id);
            DB::beginTransaction ();
            if ($request->has('image')) {
               $path  =  $this->upload($request->image,'categories-icons');
            }
           $data = [
                'image_url' => $path,
                'commesion' => $request->input ('commesion'),
                'delivery_fee' => $request->input ('delivery_fee'),
                'type' => $request->input ('type'),
                'active' => $active,
                'title' => [
                    'en' => $request->input('title')['en'],
                    'ar' => $request->input('title')['ar']
                ],
            ];
            $category->update($data);
            DB::commit();
            return redirect()->route('admin.categories.index')->with('success','Category updated successfully');
        }catch(\Exception $e){
            Log::info($e->getMessage());
            DB::rollBack();
           return redirect()->route('admin.categories.edit')->with(['error' => 'Something wrong']);
        }
    }


    public function destroy($id)
    {
        try{
            $category = $this->category->findOrFail($id);
            DB::beginTransaction();
            DB::table('sub_categories')->where('category_id',$category->id)->delete();
            $category->delete();
            DB::commit();
            return redirect()->route('admin.categories.index')->with('success','Category deleted successfully');
        }catch(\Exception $e){
            DB::rollBack();
            return redirect()->route('admin.categories.index')->with(['error' => 'Category not deleted']);
        }

    }

}
