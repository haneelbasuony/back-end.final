<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Response;

class StudentController extends Controller
{
    public function getData($id){
        $GPA = DB::select('SELECT u.user_name ,st.student_level,st.student_gpa
        FROM student AS st
        INNER JOIN user AS u ON st.user_id =u.user_id
        WHERE st.student_id =:id ',['id'=>$id]);
        return response()->Json($GPA);
    }
    public function image($id){
        $image = DB::table('student')->select('data', 'mime_type')->where('student_id', $id)->first();
        return response($image->data, 200)->header('Content-Type', $image->mime_type);
    }
}
