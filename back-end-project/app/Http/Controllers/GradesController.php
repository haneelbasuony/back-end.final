<?php

namespace App\Http\Controllers;

use App\Imports\UsersImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use League\Csv\Reader;

use App\Imports\ExcelImport;





class GradesController extends Controller
{
    // Method to retrieve all grades of a student
    public function ALL_Grades($student_id)
    {
        // Check if student with provided ID exists in the database
        if (DB::table('student')
            ->where('student_id', $student_id)
            ->exists()
        ) {
            // Retrieve all grades of the student
            $grade = DB::select('SELECT
                su.subject_name,
                su.subject_level,
                su.subject_hours,
                e.grade,
                e.score
                FROM enrolment AS e
                INNER JOIN student AS s
                ON s.student_id = e.student_id
                INNER JOIN subject AS su
                ON su.subject_code = e.subject_code
                WHERE s.student_id = :id', ['id' => $student_id]);

            // Return the grades as a JSON response
            return response()->Json($grade);
        }
        // Return error message if student with provided ID does not exist in the database
        if (DB::table('student')
            ->where('student_id', $student_id)
            ->doesntExist()
        ) {
            return response()->Json("Student ID Invalid");
        }
    }

    // Method to retrieve grades of a student for a particular level
    public function gradesLevel($student_id, $level)
    {
        // Check if student with provided ID exists in the database
        if (DB::table('student')
            ->where('student_id', $student_id)
            ->exists()
        ) {
            // Retrieve grades of the student for a particular level
            $grade = DB::select('SELECT
                su.subject_name,
                e.grade,
                e.score
                FROM enrolment AS e
                INNER JOIN student AS s
                ON s.student_id = e.student_id
                INNER JOIN subject AS su
                ON su.subject_code = e.subject_code
                WHERE s.student_id = :id
                AND su.subject_level = :level', ['id' => $student_id, 'level' => $level]);

            // Return the grades as a JSON response
            return response()->Json($grade);
        }
        // Return error message if student with provided ID does not exist in the database
        if (DB::table('student')
            ->where('student_id', $student_id)
            ->doesntExist()
        ) {
            return response()->Json("Student ID Invalid");
        }
    }

    public function importExcel(Request $request)
    {
        $file = $request->file('excel_file');
    
        // Provide the path to the imported Excel file
        $import = new ExcelImport();
        Excel::import($import, $file);
    
        // Optionally, you can perform additional tasks after the import
        return response()->Json('File succesfully uploaded');
       
    }
    
}
