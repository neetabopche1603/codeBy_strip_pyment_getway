<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // register view 
    public function registerView(){
        return view('register');
    }
    
    // Register store
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            // 'password' => 'required|min:6|confirmed',
             'password' => 'required','min:6',
             'regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x])(?=.*[!$#%]).*$/',
             'confirmed',
            'confirm_password' => 'required|min:6',
        ]);

        try {
            $register = new User();
            $register->name = $request->name;
            $register->email = $request->email;
            $register->password = Hash::make($request->password);
            $register->save();
            return redirect()->route('login')->with('success', 'Register Successfully Done!');
        } catch (Exception $e) {
            dd($e->getMessage());
        }
    }

    // register view 
    public function loginView(){
        return view('login');
    }


    
    // USER LOGIN FUNCTION
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        try {
            $credentials = $request->only('email', 'password');
            if (Auth::attempt($credentials)) {
                return redirect()->route('plans')->with('success', "User Login Successfully Done.");
            } else {
                return redirect()->route('loginGet')->with('error', "Invailid Creaditails.");
            }
        } catch (Exception $e) {
            dd($e->getMessage());
        }
    }
}
