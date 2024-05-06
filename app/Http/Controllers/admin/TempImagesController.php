<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TempImage;
use Image;

class TempImagesController extends Controller
{
   public function create(Request $request){
    $image=$request->image;
    if(!empty($image)){
       $ext=$image->getClientOriginalExtension();
       $newName=time().'.'.$ext;

       $tempImage=new TempImage();
       $tempImage->name=$newName;
       $tempImage->save();

      $image->move(public_path().'/temp',$newName);

      // generate thumbnail
      $source_path=public_path().'/temp/'.$newName;
      $destination_path=public_path().'/temp/thumb/'.$newName;
       $image=Image::make($source_path);
       $image->fit(300,275);
       $image->save($destination_path);


      return response()->json([
        'status'=>true,
        'image_id'=>$tempImage->id,
        'imagePath'=>asset('/temp/thumb/'.$newName),
        'message'=>'Image Uploaded Successfully'
      ]) ;

    }

   }
}
