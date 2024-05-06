<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;



class AuthController extends Controller
{
    public function login()
    {
        return view('front.accounts.login');
    }
    public function register()
    {
        return view('front.accounts.register');
    }

    public function processRegister(Request $request){
        $validator=Validator::make($request->all(),[
             'name'=>'required|min:3',
             'email'=>'required|email|unique:users',
             'password'=>'required|min:6|confirmed'
        ]);

        if( $validator->passes()){

            $user=new User;
            $user->name= $request->name;
            $user->email= $request->email;
            $user->phone= $request->phone;
            $user->password=Hash::make( $request->password);
            $user->save();

            session() -> flash('success', 'you have been registred successfully');
            return response()->json([
                'status'=>true,
                'message'=>'you have been registred successfully'
            ]);
        }
        else{
            return response()->json([
                'status'=>false,
                'error'=>$validator->errors()
            ]);
        }
    }

    public function authenticate(Request $request){
        $validator=Validator::make($request->all(),[
            'email'=>'required|email',
            'password'=>'required'
        ]);

        if($validator->passes()){
            if(Auth::attempt(['email'=>$request->email, 'password'=>$request->password], $request->get('remove')))
            {
                if($request->session()->has('url.intended')){
                    $url=session()->get('url.intended');
                    session()->forget('url.intended');
                    return redirect()->to($url);
                   }
                   else
                     return redirect()->route('account.profile');
                     
            }
            else{
                session()->flash('error','Either email/password is incorrect.');
                return redirect()->route('account.login')->withInput($request->only('email'));;
            }

        }
        else{
            return redirect()
            ->route('account.login')->withErrors($validator)
            ->withInput($request->only('email'));
        }
    }

    public function profile(){
        return view('front.accounts.profile');
    }

    public function logout(){
        Auth::logout();
        return redirect()->route('account.login')->with('success','You successfully Logged out');
    }
}
