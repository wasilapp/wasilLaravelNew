<?php

namespace App\Http\Controllers\Api\v1\User;

use App\Models\Shop;
use App\Models\Wallet;
use Illuminate\Http\Request;
use App\Http\Trait\MessageTrait;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class WalletController extends Controller
{
    use MessageTrait;

    private $shop;
    private $wallet;
    public function __construct(Shop $shop,Wallet $wallet)
    {
        $this->shop = $shop;
        $this->wallet = $wallet;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function walletsAcceptedByAdmin()
    {
        try {

            $wallets =  $this->wallet->where('statu', 2)->with('shop','subCategory')->get();
            if (!$wallets->isEmpty()) {
                return $this->returnData('data', ['wallets'=>$wallets]);
            } else {
                return $this->returnDataMessage('data', ['wallets'=>$wallets], trans('message.any-wallet-yet'));
            }

        }catch(\Exception $e){
            Log::info($e->getMessage());
            DB::rollBack();
            return $this->returnError('400', $e->getMessage());
        }
    }
    public function shopWalletsAcceptedByAdmin($shop)
    {
        try {
            $shop = $this->shop->find($shop);
            if(!$shop){
                return $this->errorResponse(trans('message.shop-not-found'),403);
            }

            $wallets =  $this->wallet->where('shop_id',$shop->id)->where('statu', 2)->with('shop','subCategory')->get();

            if (!$wallets->isEmpty()) {
                return $this->returnData('data', ['wallets'=>$wallets]);
            } else {
                return $this->returnDataMessage('data', ['wallets'=>$wallets], trans('message.any-wallet-yet'));
            }

        }catch(\Exception $e){
            Log::info($e->getMessage());
            DB::rollBack();
            return $this->returnError('400', $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
     * @param  \App\Models\Wallet  $wallet
     * @return \Illuminate\Http\Response
     */
    public function show(Wallet $wallet)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Wallet  $wallet
     * @return \Illuminate\Http\Response
     */
    public function edit(Wallet $wallet)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Wallet  $wallet
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Wallet  $wallet
     * @return \Illuminate\Http\Response
     */
    public function destroy(Wallet $wallet)
    {
        //
    }
}
