<?php

namespace App\Http\Controllers\Admin;

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
    public function index()
    {
        try {
            $wallets = $this->wallet->with('shop','subCategory')->orderBy('created_at', 'DESC')
                ->where('statu', 2)
                ->paginate(10);
            return view('admin.wallets.wallets')->with([
                'wallets' => $wallets
            ]);
        }catch(\Exception $e){
            Log::info($e->getMessage());
            DB::rollBack();
            return $this->returnError('400', $e->getMessage());
        }
    }
    public function walletsRequest()
    {
        try {
            $wallets = $this->wallet->with('shop','subCategory')->orderBy('created_at', 'DESC')
                ->where('statu', 1)
                ->paginate(10);
            return view('admin.wallets.requests')->with([
                'wallets' => $wallets
            ]);
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

    public function accept(Request $request, $id)
    {
        $wallet = $this->wallet->findOrFail($id);
        $wallet->update(['statu' => 2]);
        return redirect()->route('admin.wallets.index')->with('success','Successfully approved');
    }


    public function decline(Request $request, $id)
    {
        $wallet = $this->wallet->findOrFail($id);
        $wallet->update(['statu' => 3]);
        return redirect()->route('admin.wallets.index')->with('success','Rejected successfully');
    }
}
