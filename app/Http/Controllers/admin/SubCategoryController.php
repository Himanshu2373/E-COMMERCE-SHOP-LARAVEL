<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Support\Facades\Validator;

class SubCategoryController extends Controller
{
    public function index(Request $request)
    {
        $subcategories=SubCategory::select('subcategories.*', 'categories.name as categoryName')->
        latest('subcategories.id')->leftJoin('categories','categories.id', 'subcategories.category_id');

        if(!empty($request->get('keyword'))){
            $subcategories=$subcategories->where('subcategories.name','like','%'.$request->get('keyword').'%');
            $subcategories=$subcategories->orwhere('categories.name','like','%'.$request->get('keyword').'%');

        }

       $subcategories=$subcategories->paginate(10);
       $data['subcategories']=$subcategories;
       return view('admin.subcategory.list',$data);
    }
    public function  create()
    {
        $categories=Category::orderBy('name','ASC')->get();
        $data['categories']=$categories;
        return view('admin.subcategory.create',$data);
    }

    public function store(Request $request)
    {
      
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required|unique:subcategories',
            'category'=>'required',
            'status'=>'required'
        ]);

        if($validator->passes()){
            $subcategories=new SubCategory();
            $subcategories->name=$request->name;
            $subcategories->slug=$request->slug;
            $subcategories->category_id=$request->category;
            $subcategories->status=$request->status;
            $subcategories->showHome=$request->showhome;
            $subcategories->save();
             
            $request->session()->flash('success','Sub Category Added Successfully');

            return response()->json([
                'status'=>true,
                'success'=>'Sub Category Added Successfully'
            ]);


        }
        else{
            return response([
                'status'=>false,
                 'errors'=>$validator->errors()
            ]);
        }

    }

    public function  edit($id, Request $request)
    {
        $subCategory=SubCategory::find($id);
        if(empty($subCategory)){
            $request->session()->flash('error','Sub Category Not Found');
            return redirect()->route('sub-categories.index');
        }

        $categories=Category::orderBy('name','ASC')->get();
        $data['categories']=$categories;
        $data['subCategory']=$subCategory;
        return view('admin.subcategory.edit',$data);
    }

    public function update($id, Request $request)
    {
       
        $subCategory=SubCategory::find($id);

        if(empty($subCategory)){
            $request->session()->flash('error', 'SubCategory not found');
            return response()->json([
               'status'=>false,
               'notFound'=>true,
               'message'=>'SubCategory not found'
            ]);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required|unique:subcategories,slug,'.$subCategory->id.',id',
            // 'slug' => 'required|unique:subcategories',
            'category'=>'required',
            'status'=>'required'
        ]);

        if($validator->passes()){
           
            $subCategory->name=$request->name;
            $subCategory->slug=$request->slug;
            $subCategory->category_id=$request->category;
            $subCategory->status=$request->status;
            $subCategory->showHome=$request->showhome;
            $subCategory->save();
             
            $request->session()->flash('success','Sub Category Updated Successfully');

            return response()->json([
                'status'=>true,
                'success'=>'Sub Category Updated Successfully'
            ]);


        }
        else{
            return response([
                'status'=>false,
                 'errors'=>$validator->errors()
            ]);
        }


    }

    public function destroy($id, Request $request){
        $subCategory=SubCategory::find($id);

        if(!$subCategory){
            $request ->session()->flash('error','SubCategory not found');
            return response()->json([
                'status'=>false,
                'notFound'=>true,
                'message'=>'SubCategory not found'
            ]);
        }

        $subCategory->delete();
        $request->session()->flash('success', 'Category Deleted Successfully');
        return response()->json([
            'status'=>true,
            'message'=>'Category deleted Successfully'
        ]);
               
    }

}
