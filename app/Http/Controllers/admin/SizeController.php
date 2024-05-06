<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Size;
use Illuminate\Support\Facades\Validator;
class SizeController extends Controller
{
    public function index(Request $request)
    {
        $sizes = Size::latest('id');

        if($request->get('keyword')){
            $sizes=$sizes->where('sortname','like','%'.$request->get('keyword').'%');
        }

        $sizes=$sizes->paginate(10);
        return view('admin.size.list',compact('sizes'));
        
    }

    public function create(){
       return view('admin.size.create');

    }

    public function store(Request $request){
           
        $validator=Validator::make($request->all(),[
              'name'=>'required',
              'sortname'=>'required|unique:sizes'
        ]);

        if($validator->passes()){

            $sizes=new Size();
            $sizes->name=$request->name;
            $sizes->sortname=$request->sortname;
            $sizes->save();
            
            $request->session()->flash('success','Size Added Successfully');
            return response()->json([
                'status'=>true,
                'success'=>'Size Added Successfully'
            ]);
        }
        else{
            return response()->json([
                'status'=>false,
                'errors'=>$validator->errors()
            ]);
        }

    }

    public function edit($id, Request $request){
        $sizes=Size::find($id);
        if(empty($sizes)){
            $request->session()->flash('error', 'Size Not Found');
            return redirect()->route('size.index');
        }

        return view('admin.size.edit',compact('sizes'));

    }
    public function update($id, Request $request){
         
        $sizes=Size::find($id);
        if(empty($sizes)){
            $request->session()->flash('error', 'Size Not Found');
            return response()->json([
                'status'=>false,
                'notFound'=>true,
            ]);
        }

        $validator=Validator::make($request->all(),[
            'name'=>'required',
            'sortname'=>'required|unique:sizes,sortname,'.$sizes->id.',id',
    ]);

    if($validator->passes()){

      
        $sizes->name=$request->name;
        $sizes->sortname=$request->sortname;
        $sizes->save();

        $request->session()->flash('success', 'Size  Updated Successfully');
        return response()->json([
            'status'=>true,
            'message'=>'Size Updated Successfully'
        ]);

    }
    else{
        return response()->json([
            'status'=>false,
            'errors'=>$validator->errors()
        ]);
    }


    }
    
    public function destroy($id, Request $request){
               $sizes=Size::find($id);

            if(!$sizes){
                $request ->session()->flash('error','Size not found');
                return response()->json([
                    'status'=>false,
                    'notFound'=>true,
                    'message'=>'Size not found'
                ]);
            }
           $sizes->delete();
           $request->session()->flash('success','Size Deleted Successfully'); 
           return response()->json([
            'status'=>true,
            'message'=>'Size Deleted Successfully'
        ]);
    }

}
