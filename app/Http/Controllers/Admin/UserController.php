<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Manager\ProductItemController;
use App\Http\Trait\UploadImage;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\User;
use Illuminate\Http\Request;
use function PHPUnit\Framework\returnArgument;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
            $user->name = $request->name;
            $user->email = $request->email;
            $user->mobile = $request->mobile;

            if(isset($request->blocked)){
                $user->blocked = true;
            }else{
                $user->blocked = false;
            }
            if($user->save()) {
                return redirect()->route('admin.users.index')->with('success','User updated successfully');
            }
            DB::commit();
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

}
