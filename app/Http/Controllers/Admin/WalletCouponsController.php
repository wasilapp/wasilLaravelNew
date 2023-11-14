<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Shop;
use App\Models\User;
use App\Models\WalletCoupons;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;



class WalletCouponsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $coupons = WalletCoupons::orderBy('id','Desc')->paginate(5);
        return view('admin.wallet-coupons.coupons',compact(['coupons']));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $users = User::all();
        return view('admin.wallet-coupons.create-coupon',compact(['users']));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $status = 1;
        if(!$request->status){
            $status = 0;
        }
        $this->validate($request,[
            'user_number' => 'required|exists:users,id',
            'price'=> 'required',
            ]);
        DB::beginTransaction();
        try{ 
            WalletCoupons::create([
                'user_id' => $request->user_number,
                'price' => $request->price,
                'status' => $status,
                ]);
                
            DB::commit();
             return redirect(route('admin.wallet-coupons.index'))->with('message', 'Coupon is added');
        }
        catch(Exception $e){
            DB::rollBack();
            Log::info($e->getMessage());
            return redirect(route('admin.wallet-coupons.index'))->with('error', 'Coupon was not added, Something Wrong');

        }
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $shops = Shop::all();
        $coupon = WalletCoupons::where('id',$id)->first();
        $users = User::all();
        return view('admin.wallet-coupons.edit-coupon',compact(['shops','coupon','users']));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        
        $this->validate($request,[
            'user_number' => 'required|exists:users,id',
            'price'=> 'required',
            ]);
             // dd($request->all());
        DB::beginTransaction();
        try{
            WalletCoupons::where('id',$id)->update([
                'user_id' => $request->user_number,
                'price' => $request->price,
                'status' => $request->status,
                ]);
                
            DB::commit();
             return redirect(route('admin.wallet-coupons.index'))->with('message', 'Coupon is updated');
        }
        catch(Exception $e){
            DB::rollBack();
            Log::info($e->getMessage());
            return redirect(route('admin.wallet-coupons.index'))->with('error', 'Coupon was not updated, Something Wrong');

        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
