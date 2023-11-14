<?php

namespace App\Http\Controllers\Api\v1\Manager;

use App\Models\Shop;
use App\Models\Wallet;
use Illuminate\Http\Request;
use App\Http\Trait\UploadImage;
use App\Http\Trait\MessageTrait;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class WalletController extends Controller
{
    use MessageTrait;
    use UploadImage;

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
    public function index()
    {
        try {

            $wallets =  $this->wallet->where('active', 1)->where('statu', 2)->get();
            if (!$wallets->isEmpty()) {
                return $this->returnData('data', ['wallets'=>$wallets]);
            } else {
                return $this->errorResponse(trans('message.any-wallet-yet'), 200);
            }

        }catch(\Exception $e){
            Log::info($e->getMessage());
            DB::rollBack();
            return $this->returnError('400', $e->getMessage());
        }
    }
    public function myWallet()
    {
        try {
            $shop = auth()->user()->shop;
            $walletsPending = $shop->wallets()->where('statu', 1)
            ->get();
            $walletsAccepted = $shop->wallets()->where('statu', 2)
            ->get();
            $wallets = $shop->wallets;
            if (!$wallets->isEmpty()) {
                return $this->returnData('data', [
                    'walletsPending'=>$walletsPending,
                    'walletsAccepted'=>$walletsAccepted,
                    'wallets'=>$wallets
                ]);
            } else {
                return $this->errorResponse(trans('message.any-wallet-yet'), 200);
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
        try {
            $validator = Validator::make($request->all(),[
                'title.en' => 'required|unique:wallets,title->en',
                'title.ar' => 'required|unique:wallets,title->ar',
                'description.en' => 'required',
                'description.ar' => 'required',
                'image_url' => 'required',
                'usage' => 'required', 
                'price' => 'required',
                'subcategory_id' => 'required',
            ]);

            if ($validator->fails())
            {
                return $this->errorResponse($validator->errors()->all(), 422);
            }
            DB::beginTransaction ();
            $shop = auth()->user()->shop;
            if ($request->has('image_url')) {
                $path  =  $this->upload($request->image_url,'wallets');
            }
            $data = [

                'title' => [
                    'en' => $request->title['en'],
                    'ar' => $request->title['ar']
                ],
                'description' => [
                    'en' => $request->description['en'],
                    'ar' => $request->description['ar']
                ],
                'shop_id'=> $shop->id,
                'price' => $request->price,
                'usage' => $request->usage,
                'subcategory_id' => $request->subcategory_id,
                'active' => 1,
                'statu' => 1,
                'image_url' => $path,
            ];
            $wallet = $this->wallet->create($data);

            DB::commit();
            return $this->returnDataMessage('data', ['wallet'=>$wallet],trans('message.wallet-created-Please-wait-admin-approval'));
        }catch(\Exception $e){
            Log::error($e->getMessage());
            DB::rollBack();
            return $this->returnError('400', $e->getMessage());
        }

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
        try {
            
            $wallet = $this->wallet->where('id', $id)->first();
            if (!$wallet) {
                return $this->errorResponse(trans('wallet-not-found'), 400);
            }
           
            $validator = Validator::make($request->all(),[
                'title.en' => 'required|unique:wallets,title->en,' . $id,
                'title.ar' => 'required|unique:wallets,title->ar,' . $id,
                'description.en' => 'required',
                'description.ar' => 'required',
                'usage' => 'required',
                'price' => 'required',
                'subcategory_id' => 'required',
            ]);

            if ($validator->fails())
            {
                return $this->errorResponse($validator->errors()->all(), 422);
            }

            DB::beginTransaction ();
            $shop = auth()->user()->shop;
            if($shop->id <> $wallet->shop_id){
                return $this->errorResponse(trans('message.You do not have the authority to edit'), 400);
            }


            $data = [
                'title' => [
                    'en' => $request->title['en'],
                    'ar' => $request->title['ar']
                ],
                'description' => [
                    'en' => $request->description['en'],
                    'ar' => $request->description['ar']
                ],
                'shop_id'=> $shop->id,
                'price' => $request->price,
                'usage' => $request->usage,
                'subcategory_id' => $request->subcategory_id,
                'active' => $request->active,
                'statu' => 1,
            ];
            if ($request->image_url) {
                $path  =  $this->upload($request->image_url,'sub_categories');
                $data['image_url'] = $path;
            }
            $wallet->update($data);

            DB::commit();
            return $this->returnDataMessage('data', ['wallet'=>$wallet],trans('message.wallet-update-Please-wait-admin-approval'));
        }catch(\Exception $e){
            Log::error($e->getMessage());
            DB::rollBack();
            return $this->returnError('400', $e->getMessage());
        }
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
