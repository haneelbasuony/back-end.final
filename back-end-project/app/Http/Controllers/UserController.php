<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function Index(){
        $user = DB::table('user')
            ->select('user_name', 'user_email','user_password')
            ->get();
     return $user;
    }

    public function Find_User($mail,$password){
       if(DB::table('user')
       ->where('user_email', $mail)
       ->where('user_password',$password)
       ->exists()){
        $user= DB::select('SELECT student_id
        FROM student
        INNER JOIN user
        ON student.user_id = user.user_id
        WHERE user_email=:mail',['mail'=>$mail]);
        return response()->Json ($user);
        //return response()->Json ("A Registered User");
       }
       if(DB::table('user')
       ->where('user_email', $mail)
       ->where('user_password',$password)
       ->doesntExist()){
        return response()->Json("Not A Registered User GO BACK") ;
       }

       

    }
}
