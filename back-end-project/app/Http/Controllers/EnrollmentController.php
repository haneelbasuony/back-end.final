<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    public function termState($id, $level, $term)
    {
        $leveData = DB::select('SELECT s.subject_code,s.subject_name ,s.subject_hours,e.grade
        FROM subject s
        LEFT OUTER JOIN enrolment e
        ON s.subject_code = e.subject_code AND e.student_id = :id
        WHERE s.subject_level = :level AND s.Term = :term',['level'=>$level,'id'=>$id,'term'=>$term]);
         return response()->Json($leveData);
    }
}
