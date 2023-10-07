<?php

namespace App\Http\Trait;

use \Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

trait UploadImage
{
    public function upload($file , $folder)
    {
        //dd($folder);
        $filename = Str::uuid() . $file->getClientOriginalName();
        $file->move(public_path('uploads/'.$folder), $filename);
        $path = 'uploads/'. $folder .'/'. $filename;
        // $path = 'public/uploads/'. $folder .'/'. $filename;

        return $path;
    }

    public static function updateImage($old_file , $new_file, $folder){
       // dd('trait');
        $filename = Str::uuid() . $new_file->getClientOriginalName();
        $new_file->move(public_path('uploads/'.$folder), $filename);
        $path = 'uploads/'. $folder .'/'. $filename;
        Storage::disk('public')->delete($old_file);
        return $path;
    }
}
