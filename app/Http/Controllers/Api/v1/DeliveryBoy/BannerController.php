<?php

namespace App\Http\Controllers\Api\v1\DeliveryBoy;

use App\Http\Controllers\Controller;
use App\Http\Trait\MessageTrait;
use App\Models\Banner;
use App\Models\ProductImage;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    use MessageTrait;

    public function index()
    {
        $banners = Banner::where('type','driver')->get();
        return $this->returnData('data', ['banners'=>$banners]);
    }

    public function create()
    {

    }


    public function store(Request $request)
    {

    }


    public function show($id)
    {

    }


    public function edit($id)
    {


    }


    public function update(Request $request, $id)
    {


    }

    public function destroy(Request $request)
    {

    }

}
