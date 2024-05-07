<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Country;
use Illuminate\Support\Facades\Validator;
use App\Models\Shipping;


class ShippingController extends Controller
{
    public function create()
    {
        $countries=Country::get();
        $data['countries']=$countries;
        return view('admin.shipping.create',$data);
    }

    public function store(Request $request){
        $validator =Vaalidator::make($request->all(),[
            'country'=>'required',
            'amount'=>'required|numeric',
        ]);

        if($validator->passes()){
            $shipping=new Shipping();
            $shipping->country_id=$request->country;
            $shipping->amount=$request->amount;
            $shipping->save();
            
            session()->flash('success','Shipping added Successfully');
            return response()->json([
                'success'=>'Shipping added Successfully',
                 'status'=>true
            ]);
        }
        else{
            return response()->json([
                'status'=>false,
                'errors'=>$validator->errors()

            ]);
        }

    }
}
