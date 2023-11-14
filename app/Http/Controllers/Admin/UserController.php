<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Manager\ProductItemController;
use App\Http\Trait\UploadImage;
use App\Models\Banner;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\User;
use Illuminate\Http\Request;
use function PHPUnit\Framework\returnArgument;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{

    use UploadImage;
    private $user;
    private $Category;
    public function __construct(User $user,Category $Category)
    {
        $this->user = $user;
        $this->Category = $Category;
    }

    public function index()
    {
        $users_count = $this->user->get()->count();
        $users = $this->user->paginate(10);

        return view('admin.users.users')->with([
            'users' => $users,
            'users_count'=> $users_count
        ]);
    }

    public function store(Request $request)
    {

    }

    public function show($id)
    {

    }

    public function edit($id)
    {

        $user = $this->user->find($id);
        return view('admin.users.edit-user')->with([
            'user'=>$user
        ]);
    }

    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction ();
            $this->validate($request, [
                'mobile' => 'required',
            ]);
            $user= $this->user->find($id);
            if (!$user) {
                return redirect()->route('admin.users.index')->with('error', 'User not found');
            }

            $user->update([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'mobile' => $request->input('mobile'),
                'blocked' => $request->has('blocked') ? 1 : 0,
            ]);
            DB::commit();
            return redirect()->route('admin.users.index')->with('success','User updated successfully');
        }catch(\Exception $e){
            Log::info($e->getMessage());
            DB::rollBack();
             return $e->getMessage();
             return redirect()->route('admin.users.edit')->with(['error' => 'Something wrong']);
        }
    }

    public function destroy($id)
    {
        try {
           $user = $this->user->find($id);
            DB::beginTransaction();
            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }

            // $user->favorites()->detach();
            $user->carts()->delete();
            $user->addresses()->delete();
            $user->orders()->delete();
            $user->delete();
            DB::commit();
            return redirect()->route('admin.users.index')->with('success','User deleted successfully');
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            DB::rollBack();
            return redirect()->route('admin.users.index')->with(['error' => 'Something wrong']);
        }

    }

    public function getBanners(){
        $banners = Banner::where('type','user')->get();
        // dd($banners);
        return view('admin.users.banners.banners')->with([
            'banners' => $banners,
        ]);
    }
    public function createBanners(){
        return view('admin.users.banners.add-banner-images');
    }
    public function storeBanners(Request $request){
        try {
            $validator = Validator::make($request->all(),[
                'url' => 'required',
                'type' => 'required',
            ]);
            if ($validator->fails())
            {
                return redirect()->route('admin.users-banners.create')->with(['error' => $validator->errors()->all()]);
            }
            DB::beginTransaction ();
            if ($request->has('url')) {
               $url  =  $this->upload($request->url,'url_banner');
            }

            $bannerData = [
                'url' => $url,
                'type' => $request->input ('type'),
            ];
            Banner::create($bannerData);
            DB::commit();
            return redirect()->route('admin.users-banners.index')->with(['message' => 'banner has been created']);
        } catch(\Exception $e){
            Log::info($e->getMessage());
            DB::rollBack();
            return redirect()->route('admin.users-banners.create')->with(['error' => 'Something wrong']);
        }
    }

    public function destroyBanners($id){
        try {
            $banner =  Banner::findOrFail($id);
            DB::beginTransaction();
            $banner->delete();
            DB::commit();
            return redirect()->route('admin.users-banners.index')->with('success','Banner deleted successfully');
        }catch(\Exception $e){
            DB::rollBack();
            return redirect()->route('admin.users-banners.index')->with(['error' => 'Banner not deleted']);
        }
    }
}
