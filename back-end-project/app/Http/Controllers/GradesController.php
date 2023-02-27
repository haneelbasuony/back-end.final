<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GradesController extends Controller
{
    public function Grades($student_id)
    {
        if (DB::table('student')
            ->where('student_id', $student_id)
            ->exists()
        ) {
            $grade = DB::select('SELECT
        su.subject_name,
        e.grade,
        e.letter
        FROM enrolment AS e
        INNER JOIN student AS s
        ON s.student_id = e.student_id
        INNER JOIN subject AS su
        ON su.subject_id = e.subject_id
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
}
