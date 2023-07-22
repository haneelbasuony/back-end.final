<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Response;

class StudentController extends Controller
{
    public function getImage($id)
    {
        // Retrieve the profile image and MIME type of the student with the given ID
        $image = DB::table('student')->select('data', 'mime_type')->where('student_id', $id)->first();

        // Return a response with the student's profile image and MIME type
        return response($image->data, 200)->header('Content-Type', $image->mime_type);
    }

    public function updateStudentData($student_id, $student_gpa, $accepted_hours, $passedSubjects)
    {

        $student_level = number_format(($accepted_hours / 160.0) * 5.0, 2);
        $current_semesterYear = DB::select('SELECT  DISTINCT semester, year FROM `subject` ');
        $semester = $current_semesterYear[0]->semester;
        $year = $current_semesterYear[0]->year;

        if ($accepted_hours >=160 && $student_gpa >2.0) {
            DB::table('student')
                ->where('student_id', $student_id)
                ->update([
                    'student_level' => $student_level,
                    'student_gpa' => $student_gpa,
                    'accepted_hours' => $accepted_hours,
                    'passed_subjects' => $passedSubjects,
                    'graduation_semester' => $semester,
                    'graduation_year' => $year,
                    'college_state' => 'Graduated'
                ]);
                return response()->json('updated to Graduated');
        } else {
            DB::table('student')
                ->where('student_id', $student_id)
                ->update([
                    'student_level' => $student_level,
                    'student_gpa' => $student_gpa,
                    'accepted_hours' => $accepted_hours,
                    'passed_subjects' => $passedSubjects,
                    'graduation_semester' => $semester,
                    'graduation_year' => $year,
                    'college_state' => 'Undergraduate'
                ]);
                return response()->json('updated to Undergraduate');
        }
    }
}