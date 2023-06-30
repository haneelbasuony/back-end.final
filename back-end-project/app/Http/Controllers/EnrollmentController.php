<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    public function checkStudentExists($id)
    {
        $studentExists = DB::table('student')->where('student_id', $id)->exists();

        if (!$studentExists) {
            return response()->json(['error' => 'Student not found'], 404);
        }

        return response()->json(['success' => 'Student found']);
    }


    public function termState($id)
    {

        $response = $this->checkStudentExists($id);

        if ($response->getStatusCode() !== 200) {
            return $response;
        }

        // Retrieve subject information and enrollment data for the student in the given term and level
        $leveData = DB::select(
            'SELECT s.subject_level, s.Term ,s.subject_code,s.subject_name ,s.subject_hours,e.grade, s.status ,e.state AS enrolment_state
        FROM subject s
        LEFT OUTER JOIN enrolment e
        ON s.subject_code = e.subject_code AND e.student_id = :id',
            ['id' => $id]
        );
        // Return a JSON response with the enrollment data
        return response()->Json($leveData);
    }





    public function request(Request $request)
    {

        $data = $request->input('data');

        // Validate the input data if necessary

        $insertData = [];

        // Prepare the data for insertion
        foreach ($data as $row) {
            $id = $row['id'];
            $subject = $row['subject'];
            // Add each row to the insert data array
            $insertData[] = [
                'student_id' => $id,
                'subject_code' => $subject,
                'state' => 'Requested',
            ];
        }

        // Insert the rows into the enrolment table
        DB::table('enrolment')->insert($insertData);

        // Return a JSON response indicating that the data was inserted successfully
        return response()->json(['message' => 'Data inserted successfully']);
    }







    public function setGrade($studentId, $subjectId, $grade, $score, $state)
    {
        $response = $this->checkStudentExists($studentId);

        if ($response->getStatusCode() !== 200) {
            return $response;
        }

        DB::table('enrolment')
            ->where('student_id', $studentId)
            ->where('subject_code', $subjectId)
            ->update([
                'grade' => $grade,
                'score' => $score,
                'state' => $state,
            ]);
        return response()->Json('update is done');
    }





    public function getRequest()
    {
        $Request = DB::select('SELECT  us.user_name,e.student_id, e.subject_code, su.subject_name 
        FROM enrolment AS e 
        INNER JOIN subject AS su
        ON su.subject_code = e.subject_code
        INNER JOIN student AS st
        ON e.student_id=st.student_id
        INNER JOIN user AS us
        ON us.user_id=st.user_id
        WHERE state = "Requested"  ');

        return response()->Json($Request);
    }

    public function handelRequest(request $request)
    {
        $data = $request->input('data');
        foreach ($data as $row) {
            $studentId = $row['student_id'];
             $subjectCode = $row['subject_code'];
             $enrolmentState =$row['state'];
             // Add each row to the insert data array
             DB::table('enrolment')
            ->where('student_id', $studentId)
            ->where('subject_code', $subjectCode)
            ->update(['state'=>$enrolmentState]); 
         }
         return response()->json(['message' => 'Data updated successfully']);
    }
}
