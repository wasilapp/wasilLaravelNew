<?php

namespace App\Http\Trait;

use \Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;


trait MessageTrait
{
    public function SuccessMessage($route,$msg )
    {
        return redirect ()->route ($route)
            ->with('message', 'Data ' .$msg. ' successfully');
    }

    public function ErrorMessage($route,$msg )
    {
        return redirect ()->route ($route)
            ->withErrors('error',  $msg);
    }

    public function returnData($key, $data): JsonResponse
    {
        return response()->json(
            [
                $key => $data,
                'status' => true,
                'stateNum' => '200',
                'message' => trans('message.done-success')
            ]
        )->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', '*');
    }

    public function returnDataMessage($key, $data,$msg): JsonResponse
    {
        return response()->json(
            [
                $key => $data,
                'status' => true,
                'stateNum' => '200',
                'message' => $msg
            ]
        )->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', '*');
    }

    public function returnMessage($msg,$code): JsonResponse
    {
        return response()->json(
            [
                'status' => true,
                'stateNum' => $code,
                'message' => $msg
            ]
        )->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', '*');
    }

    public function returnError($stateNum, $msg): JsonResponse
    {
        return response()->json(
            [
                'status' => false,
                'stateNum' => $stateNum,
                'message' => $msg
            ])->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', '*');
    }

     public function errorResponse($message, int $code = ResponseAlias::HTTP_BAD_REQUEST): JsonResponse
    {
        return response()->json(['error' => $message,'stateNum'=>$code], $code);
    }
}
