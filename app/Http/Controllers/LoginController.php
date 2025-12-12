<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Auth;

class LoginController extends Controller
{
    public function show(){
        return view('login');
    }
    public function check(Request $req){
        $user = User::where('email',$req->username)->first();
        if($user && Hash::check($req->password, $user->password)){
            session(['admin_logged_in' => true, 'user_id' => $user->id]);
            Auth::login($user);
            return redirect()->route('dashboard');
        }
        else{
            return redirect()->route('login.show')->with('error','Invalid password');
        }
    }
}
