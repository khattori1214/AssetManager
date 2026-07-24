<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use app\Models\User;


class LoginController extends Controller
{
    //
    public function index(){
        return view('auth.login');
    }

    public function login(Request $request){
        $employee_no=$request->employee_no;
        $password=$request->password;

        if(empty($employee_no)){
            return '社員番号IDを入力してください';
        }
        if(empty($password)){
            return '社員番号IDを入力してください';
        }else{
            return view('top.index');
        }
    }

     public function logout(Request $request){
        
        return redirect('/login');
        }

}
