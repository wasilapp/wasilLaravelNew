<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Trait\UploadImage;
use App\Models\Banner;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class BannerController extends Controller
{

    use UploadImage;

    public function index()
    {
        $banners = Banner::all();

        foreach ($banners as $banner) {
            $banner->url = asset('storage/' . $banner->url);
        }

        return view('admin.banners.edit-banner-images')->with([
            'banners' => $banners,
        ]);
    }

    public function create()
    {
        return view('admin.banners.add-banner-images');

    }


    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(),[
                'url' => 'required',
                'target' => 'required',
            ]);
            if ($validator->fails())
            {
                return redirect()->route('admin.banners.add-banner-images')->with(['error' => $validator->errors()->all()]);
            }
            DB::beginTransaction ();
            if ($request->has('url')) {
               $url  =  $this->upload($request->url,'url_banner');
            }

            $bannerData = [
                'url' => $url,
                'target' => $request->input ('type'),
            ];
            Banner::create($bannerData);
            DB::commit();
            return redirect()->route('admin.banners.add-banner-images')->with(['message' => 'banner has been created']);
        } catch(\Exception $e){
            Log::info($e->getMessage());
            DB::rollBack();
            return redirect()->route('admin.banners.add-banner-images')->with(['error' => 'Something wrong']);
        }
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

        $this->validate($request, [
            'id' => 'required'
        ]);

        return Banner::deleteImage($request->id);
    }

}
