<?php

namespace App\Http\Controllers\Api\v1\User;

use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use App\Http\Trait\MessageTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class UserAddressController extends Controller
{
    use MessageTrait;

    private $address;
    public function __construct(UserAddress $address)
    {
        $this->address = $address;
    }
    public function index(Request $request)
    {
        $user_id = $request->user()->id;
        $addresses = UserAddress::where('user_id',$user_id)->where('active',true)->get();

        if ($addresses) {
            return $this->returnData('data', ['addresses'=>$addresses]);
        } else {
            return $this->errorResponse(trans('message.any-addresses-yet'), 200);
        }

    }

    public function setDefaultAddress(Request $request, $addressId)
    {
        $user_id = $request->user()->id;

        UserAddress::where('user_id', $user_id)->where('default', true)->update(['default' => false]);

        UserAddress::where('user_id', $user_id)->where('id', $addressId)->update(['default' => true]);

        return $this->returnMessage(trans('message.default-address-set'), 200);
    }

    public function getDefaultAddress(Request $request)
    {
        $user_id = $request->user()->id;

        $defaultAddress = UserAddress::where('user_id', $user_id)->where('default', true)->first();

        if (!$defaultAddress) {
            return $this->errorResponse(trans('message.no-default-address'), 404);
        }

        return $this->returnData('data', ['default_address' => $defaultAddress]);
    }

    public function create()
    {

    }


    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(),[
                'longitude' => 'required',
                'latitude' => 'required',
                'address' => 'required',
                'city' => 'required',
                'pincode' => 'required',
                'type' => 'required',
            ]);

            if ($validator->fails())
            {
                return $this->errorResponse($validator->errors()->all(), 422);
            }
            DB::beginTransaction ();
            $user_id = auth()->user()->id;
            $data = [
                'longitude' => $request->longitude,
                'latitude' => $request->latitude,
                'address' => $request->address,
                'address2' => $request->address2,
                'city' => $request->city,
                'pincode' => $request->pincode,
                'user_id' => $user_id,
                'type' => $request->type,
            ];
            if(isset($request->default)){
                if($request->default){
                    UserAddress::setAllDefaultOff($user_id);
                }
                $data['default'] = $request->default;

            }
            if ($request->type == 0 ){
                $address = $this->address->where('user_id' ,$user_id)->where('type' , 0)->first();
                if($address){
                    $address->update($data);
                    DB::commit();
                    return $this->returnDataMessage('data', ['Address'=>$address],trans('message.address-updated'));
                }
            }
            $address = $this->address->create($data);

            DB::commit();
            return $this->returnDataMessage('data', ['Address'=>$address],trans('message.address-added'));
        }catch(\Exception $e){
            Log::error($e->getMessage());
            DB::rollBack();
            return response(['errors' => [$e->getMessage()]], 402);
        }

    }

    public function show($id)
    {
    }


    public function edit($id)
    {

    }

    public function update(Request $request)
    {
        try {
            $validator = Validator::make($request->all(),[
                'longitude' => 'required',
                'latitude' => 'required',
                'address' => 'required',
                'city' => 'required',
                'pincode' => 'required',
                'type' => 'required',
            ]);

            if ($validator->fails())
            {
                return $this->errorResponse($validator->errors()->all(), 422);
            }
            DB::beginTransaction ();
            $user_id = auth()->user()->id;

            $data = [
                'longitude' => $request->longitude,
                'latitude' => $request->latitude,
                'address' => $request->address,
                'address2' => $request->address2,
                'city' => $request->city,
                'pincode' => $request->pincode,
                'user_id' => $user_id,
                'type' => $request->type,
            ];
            if(isset($request->default)){
                if($request->default){
                    UserAddress::setAllDefaultOff($user_id);
                }
                $data['default'] = $request->default;
            }

            $address = $this->address->where('id' ,$request->id)->first();

            if($address){
                $address->update($data);
                DB::commit();
                return $this->returnDataMessage('data', ['Address'=>$address],trans('message.address-updated'));
            } else {
                return $this->errorResponse(trans('message.address-not-found'), 403);
            }


        }catch(\Exception $e){
            Log::error($e->getMessage());
            DB::rollBack();
            return response(['errors' => [$e->getMessage()]], 402);
        }

    }


    public function destroy($id){
        try {
            DB::beginTransaction();
            $userAddress = UserAddress::find($id);
            if($userAddress){
                $userAddress->delete();
                DB::commit();
                return $this->returnMessage(trans('message.Address is deleted'),200);
            }else {
                return $this->errorResponse(trans('message.address-not-found'), 403);
            }

        } catch (\Exception $e) {
            Log::error($e->getMessage());
            DB::rollBack();
            return response(['errors' => [$e->getMessage()]], 402);
        }

    }

}
