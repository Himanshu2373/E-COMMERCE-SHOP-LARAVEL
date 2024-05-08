<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Gloudemans\Shoppingcart\Facades\Cart;
use App\Models\Product;
use App\Models\Country;
use App\Models\ShippingCharge;
use App\Models\CustomerAddress;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Order;
use App\Models\OrderItem;

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

        $customerAddress=CustomerAddress::where('user_id',Auth::user()->id)->first();
        session()->forget('url.intended');
        $countries=Country::orderBy('name','ASC')->get();

        // calculate shipping Here
        if($customerAddress!=null){
            $userCountry=$customerAddress->country_id;
            $shippingInfo=ShippingCharge::where('country_id',$userCountry)->first();
             
            // count item qty
            $totalQty=0;
            $shippingCharge=0;
            $grandTotal=0;
            foreach(Cart::content() as $item){
                $totalQty+=$item->qty;
            }
             $shippingCharge=  $totalQty*$shippingInfo->amount;
             $grandTotal=Cart::subtotal(2,'.','')+ $shippingCharge;
    
        }
         else{
            $shippingCharge=0;
            $grandTotal=Cart::subtotal(2,'.','');
         }
       

        // if user is logged in then redirect checkout page
        return view('front.checkout',[
            'countries'=>$countries,
            'customerAddress'=> $customerAddress,
            'shippingCharge'=>$shippingCharge,
            'grandTotal'=>$grandTotal
        ]);

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
         $user=Auth::user();

        CustomerAddress::updateOrCreate(
            ['user_id'=>$user->id],
            [
                'user_id'=>$user->id,
                'first_name'=>$request->first_name,
                'last_name'=>$request->last_name,
                'email'=>$request->email,
                'mobile'=>$request->mobile,
                'country_id'=>$request->country,
                'address'=>$request->address,
                'apartment'=>$request->apartment,
                'city'=>$request->city,
                'state'=>$request->state,
                'zip'=>$request->zip,
                'notes'=>$request->notes
                ]
        );

        // step-3 save data in order tables
        if($request->payment_method=='cod'){

            // $shipping=0;
            $discount=0;
            $subTotal=Cart::subtotal(2,'.','');
            // $grandTotal= $subTotal+$shipping;

            // calculate shiiping charge 
            $shippingInfo=ShippingCharge::where('country_id',$request->country)->first();

            $totalQty=0;
            foreach(Cart::content() as $item){
                $totalQty+=$item->qty;
              }

            if($shippingInfo != null){
                $shippingCharge=$totalQty*$shippingInfo->amount;
                $grandTotal=$subTotal+$shippingCharge;
             }
             else{
                 $shippingInfo=ShippingCharge::where('country_id','Rest_of_world')->first();
            
                 $shippingCharge=$totalQty*$shippingInfo->amount;
                 $grandTotal=$subTotal+$shippingCharge;

                }

            $order=new Order;
            $order->user_id=$user->id;
            $order->subtotal=$subTotal;
            $order->discount=$discount;
            $order->shipping=$shippingCharge;
            $order->grandtotal=$grandTotal;

            $order->first_name=$request->first_name;
            $order->last_name=$request->last_name;
            $order->email=$request->email;
            $order->mobile=$request->mobile;
            $order->country_id=$request->country;
            $order->address=$request->address;
            $order->apartment=$request->apartment;
            $order->city=$request->city;
            $order->state=$request->state;
            $order->zip=$request->zip;
            $order->notes=$request->order_notes;
            $order->save();


            //  step-4  sotre order items in orderItems table

            foreach (Cart::content() as $item) {
                $orderItem=new OrderItem;
                $orderItem->order_id=$order->id;
                $orderItem->product_id=$item->id;
                $orderItem->name=$item->name;
                $orderItem->price=$item->price;
                $orderItem->qty=$item->qty;
                $orderItem->total=$item->price*$item->qty;
                $orderItem->save();
            }
              session()->flash('success','Order placed successfully');
              Cart::destroy();
            return response()->json([
                'status'=>true,
                'orderId'=>$order->id,
                'message'=>'Order saved successfully'
                
               ]);
        }
        else
        {

        }
     }

     public function thankyou($id){
        return view('front.thank',[
            'id'=>$id
        ]);
     }

     public function getOrderDetails(Request $request){

           $subTotal=Cart::subtotal(2,'.','');
           if($request->country_id>0){
           
            $shippingInfo=ShippingCharge::where('country_id',$request->country_id)->first();

            $totalQty=0;
            foreach(Cart::content() as $item){
                $totalQty+=$item->qty;
              }

              if($shippingInfo != null){
                    $shippingCharge=$totalQty*$shippingInfo->amount;
                    $grandTotal=$subTotal+$shippingCharge;

                    return response()->json([
                        'status'=>true,
                        'shippingCharge'=>number_format($shippingCharge,2),
                        'grandTotal'=>number_format($grandTotal,2)
                    ]);
              }
              else{
                $shippingInfo=ShippingCharge::where('country_id','Rest_of_world')->first();
                
                $shippingCharge=$totalQty*$shippingInfo->amount;
                $grandTotal=$subTotal+$shippingCharge;

                return response()->json([
                    'status'=>true,
                    'shippingCharge'=>number_format($shippingCharge,2),
                    'grandTotal'=>number_format($grandTotal,2)
                ]);
               }

           }
            else{
                return response()->json([
                    'status'=>true,
                    'shippingCharge'=>0,
                    'grandTotal'=> number_format($subTotal,2)
                ]);
            }
     }

}
