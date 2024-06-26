<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\subCategory;

class ProductSubCategoryController extends Controller
{
    public function index(Request $request){

        if(!empty($request->category_id)){
            $subCategories=subCategory::where('category_id',$request->category_id)->orderBy('id','ASC')->get();
        return response()->json([
            'status'=>true,
            'subCategories'=>$subCategories
        ]);

        }
        else{
            return response()->json([
                'status'=>true,
                'subCategories'=>[]
            ]);
        }
       
    }
}
