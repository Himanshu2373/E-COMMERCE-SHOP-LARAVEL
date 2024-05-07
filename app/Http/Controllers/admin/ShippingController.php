<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Country;
use Illuminate\Support\Facades\Validator;
use App\Models\ShippingCharge;


class ShippingController extends Controller
{
    public function create()
    {
        $countries=Country::get();

        $shippingCharges=ShippingCharge::select('shipping_charges.*','countries.name')->leftJoin('countries','countries.id','shipping_charges.country_id')->get();
        $data['countries']=$countries;
        $data['shippingCharges']=$shippingCharges;
        return view('admin.shipping.create',$data);
    }

    public function store(Request $request){
       
        $validator =Validator::make($request->all(),[
            'country'=>'required',
            'amount'=>'required|numeric',
        ]);

        if($validator->passes()){

            $count=ShippingCharge::where('country_id',$request->country)->count();
        
            if($count>0){
                session()->flash('error','Shipping detailes already Exist');
                return response()->json([
                    'status'=>true
                   
                ]);
            }

            $shippingCharges=new ShippingCharge;
            $shippingCharges->country_id=$request->country;
            $shippingCharges->amount=$request->amount;
            $shippingCharges->save();
            
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

    public function edit($id, Request $request){
        $shippingCharges=ShippingCharge::find($id);
        if(empty($shippingCharges)){
            $request->session()->flash('error', 'ShippingCharges Not Found');
            return redirect()->route('shipping.create');
        }
        $countries=Country::get();
        $data['countries']=$countries;
        $data['shippingCharges']=$shippingCharges;

        return view('admin.shipping.edit',$data);

    }

    public function update($id, Request $request){
         
        $shippingCharges=ShippingCharge::find($id);
        if(empty($shippingCharges)){
            $request->session()->flash('error', 'ShippingCharges Not Found');
            return redirect()->route('shipping.create');
        }

        $validator =Validator::make($request->all(),[
            'country'=>'required',
            'amount'=>'required|numeric',
        ]);

        if($validator->passes()){
            
            $shippingCharges->country_id=$request->country;
            $shippingCharges->amount=$request->amount;
            $shippingCharges->save();
            
            session()->flash('success','Shipping updated Successfully');
            return response()->json([
                'success'=>'Shipping updated Successfully',
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
    
    public function destroy($id, Request $request){
        $shippingCharges=ShippingCharge::find($id);

        if(empty($shippingCharges)){
            $request->session()->flash('error', 'ShippingCharges Not Found');
            return redirect()->route('shipping.create');
        }

        $shippingCharges->delete();
        $request->session()->flash('success', 'ShippingCharges Deleted Successfully');
        return response()->json([
            'status'=>true,
            'message'=>'ShippingCharges deleted Successfully'
        ]);
               
    }
}
