<?php

namespace App\Http\Controllers\Api\v1\User;

use App\Http\Controllers\Controller;
use App\Models\WalletCoupons;
use App\Models\UserCoupon;
use Illuminate\Http\Request;
class WalletCouponsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $coupons = WalletCoupons::where('user_id',auth()->id())->where('status',1)->whereColumn('usage' ,'<' ,'price')->orderBy('id','Desc')->get();
        $data['coupons'] = $coupons->toArray();
        $data['total'] = array_sum($coupons->pluck('price')->toArray());
        return $data;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function userCoupons()
    {
        $data = [];
        
        $coupons = WalletCoupons::where('user_id',auth()->id())->where('status',1)->whereColumn('usage' ,'<' ,'price')->orderBy('id','Desc')->get();
        $data['coupons'] = $coupons->toArray();
        $data['total'] = array_sum($coupons->pluck('price')->toArray());
        return $data;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $coupon = WalletCoupons::where('id',$id)->first();
        return $coupon;
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
            'shop_number' => 'required|exists:shops,id',
            'max_usage' => 'required',
            'price'=> 'required',
            'current_usage'=> 'required',
            ]);
             // dd($request->all());
        DB::beginTransaction();
        try{
            WalletCoupons::where('id',$id)->update([
                'user_id' => $request->user_number,
                'shop_id' => $request->shop_number,
                'price' => $request->price,
                'max_usage' => $request->max_usage,
                'current_usage' => $request->current_usage,
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
