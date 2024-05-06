<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductImage;
use Image;
use Illuminate\Support\Facades\File;

class ProductImageController extends Controller
{
    public function update(Request $request)
    {   
         
        $image=$request->image;
        $ext=$image->getClientOriginalExtension();
        $sourcePath=$image->getPathName();

        $productsImage=new ProductImage();
        $productsImage->product_id=$request->product_id;
        $productsImage->image='NULL';
        $productsImage->save();

        
        $imageName= $request->product_id.'-'.$productsImage->id.'-'.time().'-'.$ext;
        $productsImage->image=$imageName;
        $productsImage->save();

         //large image

         $destPath=public_path().'/uploads/product/large/'.$imageName;
         $image=Image::make($sourcePath);
         $image->resize(1400,null,function($constraint){
             $constraint->aspectRatio();
         }); 
         $image->save($destPath);

        //small image

        $destPath=public_path().'/uploads/product/small/'.$imageName;
        $image=Image::make($sourcePath);
        $image->fit(300,300);
        $image->save($destPath);

        return response()->json([
            'status'=>true,
            'image_id'=>$productsImage->id,
            'imagePath'=>asset('uploads/product/small/'.$productsImage->image),
            'message'=>'Image saved successfully',
        ]);
    }

    public function destroy(Request $request){
        $productsImage=ProductImage::find($request->id);
       
        if(empty($productsImage)){
            $request->session()->flash('error', 'Image not found');
            return response()->json([
               'status'=>false,
               'notFound'=>true,
               'message'=>'Image not found'
            ]);
        }
        // delete image from folder
        File::delete(public_path('uploads/product/large/'.$productsImage->image));
        File::delete(public_path('uploads/product/small/'.$productsImage->image));
        
        $productsImage->delete();

        return response()->json([
            'status'=>true,
            'message'=>'Image deleted successfully',
        ]);
    }
}