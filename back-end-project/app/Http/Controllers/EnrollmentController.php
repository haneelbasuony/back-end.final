<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    /**
     * Get enrollment data for a student in a given term and level.
     *
     * @param int $id The ID of the student.
     * @param int $level The level of the subjects.
     * @param string $term The term of the subjects.
     * @return Illuminate\Http\JsonResponse A JSON response with the enrollment data.
     */
    public function termState($id, $level, $term)
    {
        // Retrieve subject information and enrollment data for the student in the given term and level
        $leveData = DB::select('SELECT s.subject_code,s.subject_name ,s.subject_hours,e.grade
        FROM subject s
        LEFT OUTER JOIN enrolment e
        ON s.subject_code = e.subject_code AND e.student_id = :id
        WHERE s.subject_level = :level AND s.Term = :term', ['level' => $level, 'id' => $id, 'term' => $term]);

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
}
