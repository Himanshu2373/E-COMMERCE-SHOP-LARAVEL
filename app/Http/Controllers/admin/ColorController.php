<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Color;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class ColorController extends Controller
{
    public function index(Request $request){
        $colors=Color::latest('id');
          
          if($request->get('keyword')){
            $colors=$colors->where('name','like','%'.$request->get('keyword').'%');

        }

          $colors=$colors->paginate(10);
          return view("admin.color.list", compact('colors'));
    }

    public function create(){
        return view('admin.color.create');
    }

    public function store(Request $request){

        $validator=Validator::make($request->all(),[
            'name'=>'required',
            'colorcode'=>'required|unique:colors',
    ]);

    if($validator->passes()){

        $color=new Color();
        $color->name=$request->name;
        $color->colorcode=$request->colorcode;
        $color->save();

        $request->session()->flash('success', 'Color Added Successfully');
        return response()->json([
            'status'=>true,
            'message'=>'Color Added Successfully'
        ]);

    }
    else{
        return response()->json([
            'status'=>false,
            'errors'=>$validator->errors()
        ]);
    }

    

    }

    public function edit($id,Request $request){
        $colors=Color::find($id);
        if(empty($colors)){
            $request->session()->flash('error', 'Color Not Found');
            return redirect()->route('color.index');
        }

        return view('admin.color.edit',compact('colors'));

    }
    public function update($id, Request $request){
         
        $colors=Color::find($id);
        if(empty($colors)){
            $request->session()->flash('error', 'Color Not Found');
            return response()->json([
                'status'=>false,
                'notFound'=>true,
            ]);
        }

        $validator=Validator::make($request->all(),[
            'name'=>'required',
            'colorcode'=>'required|unique:colors,colorcode,'.$colors->id.',id',
    ]);

    if($validator->passes()){

      
        $colors->name=$request->name;
        $colors->colorcode=$request->colorcode;
        $colors->save();

        $request->session()->flash('success', 'Color Updated Successfully');
        return response()->json([
            'status'=>true,
            'message'=>'Color Updated Successfully'
        ]);

    }
    else{
        return response()->json([
            'status'=>false,
            'errors'=>$validator->errors()
        ]);
    }


    }
    public function destroy($id , Request $request){
        $colors=Color::find($id);

        if(!$colors){
            $request ->session()->flash('error','Color not found');
            return response()->json([
                'status'=>false,
                'notFound'=>true,
                'message'=>'Color not found'
            ]);
        }

        $colors->delete();
        $request->session()->flash('success', 'Color Deleted Successfully');
        return response()->json([
            'status'=>true,
            'message'=>'Color deleted Successfully'
        ]);
               

    }
}
