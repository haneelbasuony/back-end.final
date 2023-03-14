<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GradesController extends Controller
{
    public function ALL_Grades($student_id)
    {
        if (DB::table('student')
            ->where('student_id', $student_id)
            ->exists()
        ) {
        $grade = DB::select('SELECT
        su.subject_name,
        e.grade
        FROM enrolment AS e
        INNER JOIN student AS s
        ON s.student_id = e.student_id
        INNER JOIN subject AS su
        ON su.subject_code = e.subject_code
        WHERE s.student_id = :id', ['id' => $student_id]);
            return response()->Json($grade);
        }
        if (DB::table('student')
            ->where('student_id', $student_id)
            ->doesntExist()
        ) {
            return response()->Json("Student ID Invalid");
        }
    }


    public function gradesLevel($student_id, $level)
    {
        if (DB::table('student')
            ->where('student_id', $student_id)
            ->exists()
        ) {
            $grade = DB::select('SELECT
            su.subject_name,
            e.grade
            FROM enrolment AS e
            INNER JOIN student AS s
            ON s.student_id = e.student_id
            INNER JOIN subject AS su
            ON su.subject_code = e.subject_code
            WHERE s.student_id = :id
            AND su.subject_level = :level' , ['id' => $student_id,'level'=>$level]);
            return response()->Json($grade);
        }
        if (DB::table('student')
            ->where('student_id', $student_id)
            ->doesntExist()
        ) {
            return response()->Json("Student ID Invalid");
        }
    }
}
