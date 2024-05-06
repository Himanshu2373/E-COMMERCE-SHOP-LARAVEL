<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Gloudemans\Shoppingcart\Facades\Cart;
use App\Models\Product;
use App\Models\Country;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class CartController extends Controller
{
    public function addToCart(Request $request)
    {
        $products=Product::with('product_images')->find($request->id);

        if($products==null){
            return response()->json([
                'status'=>false,
                'message'=>'Product not found'
            ]);
        } 

        if(Cart::count()>0){
            // check cart is empty or not and if not then again check
            // product is already exist in cart or not if not then add otherwise return a message
            $cartContent=Cart::content();
            $productAlredyExist=false;

            foreach($cartContent as $item){
                if($item->id==$products->id){
                    $productAlredyExist=true;
                    }
                  }
            if($productAlredyExist==false){
                Cart::add($products->id, $products->title, 1, $products->price,['productImage'=>(!empty($products->product_images))? $products->product_images->first():'']);
                $status=true;
                $message='<strong>'.$products->title.'<strong> added in cart successfully';
                session()->flash('success',$message);
              }
            else{
                $status=false;
                $message= $products->title.' already added in cart';
            }
            
        }
        else{
          
            Cart::add($products->id, $products->title, 1, $products->price,['productImage'=>(!empty($products->product_images))? $products->product_images->first():'']);
            $status=true;
            $message='<strong>'.$products->title.'<strong> added in cart successfully';
            session()->flash('success',$message);
        
        }

        return response()->json([
            'status'=> $status,
            'message'=>$message
        ]);

    }

    public function cart(){
        $cartContent=Cart::content();
        $data['cartContent']= $cartContent;
        return view('front.cart',$data);

    }

    public function cartUpdate(Request $request){
        $rowId=$request->rowId;
        $qty= $request->qty;

        // check stock Avialable or not
        $itemInfo=Cart::get($rowId);
        $productInfo=Product::find($itemInfo->id);

        if( $productInfo->track_qty=='Yes'){
            if($productInfo->qty >= $qty){
                Cart::update($rowId,$qty);
                $message='Cart updated successfully';
                $status=true;
                session()->flash('success',$message);
            }
            else
            {
                $message='Requested Qty('.$qty.') not avaialable in stock';
                $status=false;
                session()->flash('error',$message);
            }

        }
        else{
            Cart::update($rowId,$qty);
            $message='Cart updated successfully';
            $status=true;
            session()->flash('success',$message);
        }

        return response()->json([
            'status'=>$status,
            'message'=>$message
        ]);
    }

    public function cartDelete(Request $request){
        $itemInfo=Cart::get($request->rowId);
        if( $itemInfo==null){
            $message='item not found in cart ';
            session()->flash('error',$message);
            return response()->json([
                'status'=>false,
                'message'=>$message
            ]);
        }

        Cart::remove($request->rowId);
        $message='Cart item deleted successfully';
        session()->flash('success',$message);
        return response()->json([
            'status'=>true,
            'message'=>$message
        ]);
    
     }


     public function checkout(){
        
        // if cart is empty redirect cart page
        if(Cart::count()==0){
            return redirect()->route('front.cart');
        }

        // if user is not logged in then redirect login page
        if(Auth::check()==false){

            if(!(session()->has('url.intended'))){
                session(['url.intended' => url()->current()]);
            }
    
            return redirect()->route('account.login');
        }

        $countries=Country::orderBy('name','ASC')->get();
        // if user is logged in then redirect checkout page
        return view('front.checkout',['countries'=>$countries]);

     }

     public function processcheckout(Request $request){
    //   step-1  apply validator
         $validator=Validator::make(request()->all(),[
               'first_name'=>'required|min:3',
               'last_name'=>'required',
               'email'=>'required|email',
               'country'=>'required',
               'mobile'=>'required',
               'address'=>'required|min:10',
               'city'=>'required',
               'state'=>'required',
               'zip'=>'required'
       ]);
       if($validator->fails()){
           return response()->json([
            'status'=>false,
            'message'=>'please fill the currect value',
            'errors'=>$validator->errors()
           ]);
       }

        //  step-2  save customer address
        
        
     }

    }
