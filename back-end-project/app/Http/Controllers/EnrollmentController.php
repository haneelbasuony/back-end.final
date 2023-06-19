<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    public function termState($id)
    {
        // Retrieve subject information and enrollment data for the student in the given term and level
        $leveData = DB::select(
            'SELECT s.subject_level, s.Term ,s.subject_code,s.subject_name ,s.subject_hours,e.grade
        FROM subject s
        LEFT OUTER JOIN enrolment e
        ON s.subject_code = e.subject_code AND e.student_id = :id',
            ['id' => $id]
        );

        // Return a JSON response with the enrollment data
        return response()->Json($leveData);
    }


    public function Request($id, $subject)
    {
        // Insert a new row into the enrolment table with the student ID, subject code, and a state of "Requested"
        DB::insert('INSERT INTO enrolment (student_id,subject_code,state)
                    VALUES (?, ?, "Requested")', [$id, $subject]);

        // Return a JSON response indicating that the data was inserted successfully
        return response()->json(['message' => 'Data inserted successfully']);
    }
    public function setGrade($studentId, $subjectId, $grade,$score)
    {
        DB::table('enrolment')
        ->where('student_id', $studentId)
        ->where('subject_code', $subjectId)
        ->update([
            'grade' => $grade,
            'score' => $score,
        ]);
        return response()->Json('update is done');
    }
}
