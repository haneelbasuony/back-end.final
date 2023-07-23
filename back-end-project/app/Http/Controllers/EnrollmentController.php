<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\NotificationController;

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
        // ADD 1 column regestiration status	1 column dropable 

        // Retrieve subject information and enrollment data for the student in the given term and level
        $leveData = DB::select(
            'SELECT s.submition, s.dropablitiy, s.subject_level, s.Term ,s.subject_code,s.subject_name ,s.subject_hours,e.grade, s.status ,e.state AS enrolment_state, s.semester,s.year
        FROM subject s
        LEFT OUTER JOIN enrolment e
        ON s.subject_code = e.subject_code AND e.student_id = :id',
            ['id' => $id]
        );
        // Return a JSON response with the enrollment data
        return response()->Json($leveData);
    }





    public function enrolmentState(Request $request)
    {

        $data = $request->input('data');

        // Validate the input data if necessary

        $insertData = [];

        foreach ($data as $row) {


            $id = $row['student_id'];
            $subject = $row['subject_code'];
            $state = $row['state'];
            $cureentsubjectYear = DB::select('SELECT `year` FROM `subject`')[0]->year;
            $currnetsubjectSemester = DB::select('SELECT `semester` FROM `subject`')[0]->semester;

            $trialadvisor_id = DB::select('SELECT gr.advisor_id
               FROM `group` AS gr
               INNER JOIN student AS st
               ON gr.group_id = st.group_id
               WHERE st.student_id = :id', [$id]);

            $advisor_id = $trialadvisor_id[0]->advisor_id;

            $notificationController = new NotificationController();
            $notificationController->addAdvisorNotification($advisor_id, 'You have an unseen subject request from ' . $id);

            $trialCount = DB::select('SELECT COUNT(student_id) As count FROM enrolment
               WHERE student_id = :id AND subject_code= :subjectCode', ['id' => $id, 'subjectCode' => $subject]);
            $count = $trialCount[0]->count;

            if (
                DB::table('enrolment')
                    ->where('student_id', $id)
                    ->where('subject_code', $subject)
                    ->exists()
            ) {
                if (
                    DB::table('enrolment')
                        ->join('subject', 'enrolment.subject_code', '=', 'subject.subject_code')
                        ->where(
                            'enrolment.year',
                            $cureentsubjectYear
                        )
                        ->where(
                            'enrolment.semester',
                            $currnetsubjectSemester
                        )
                        ->exists()
                ) {
                    DB::table('enrolment')
                        ->where('student_id', $id)
                        ->where('subject_code', $subject)
                        ->update([
                            'state' => $state,
                        ]);
                } else {
                    $insertData[0] = [
                        'student_id' => $id,
                        'subject_code' => $subject,
                        'state' => $state,
                        'trial' => $count,
                        'year' => $cureentsubjectYear,
                        'semester' => $currnetsubjectSemester,
                    ];
                    DB::table('enrolment')->insert($insertData);

                }
            } else {
                $insertData[0] = [
                    'student_id' => $id,
                    'subject_code' => $subject,
                    'state' => $state,
                    'trial' => '0',
                    'year' => $cureentsubjectYear,
                    'semester' => $currnetsubjectSemester,
                ];
                DB::table('enrolment')->insert($insertData);
            }

        }
        // Return a JSON response indicating that the data was inserted successfully
        return response()->json(['message' => 'Data inserted successfully']);

    }


    public function setGrade(Request $request)
    {
        $data = $request->input('data');

        //  studentID-Subjectcode-classwork-final-grade-state

        foreach ($data as $row) {
            $studentID = $row['studentID'];
            $subjectCode = $row['courseCode'];
            $classwork = $row['classWork'];
            $final = $row['final'];
            $grade = $row['grade'];
            $score = $row['score'];
            $state = $row['state'];
            $examState = $row['examState'];

            // Add each row to the insert data array
            DB::table('enrolment')
                ->where('student_id', $studentID)
                ->where('subject_code', $subjectCode)
                ->update([
                    'classwork' => $classwork,
                    'final' => $final,
                    'grade' => $grade,
                    'score' => $score,
                    'state' => $state,
                    'exam_state' => $examState,
                ]);
        }

        // return response()->Json($updatedData);
        return response()->Json('update is done');
    }




    public function getRequestCount()
    {
        $query = DB::select('SELECT su.year,su.semester, e.subject_code, su.subject_name , i.instructor_name, COUNT(*) AS request_count
        FROM enrolment AS e 
        INNER JOIN subject AS su
        ON su.subject_code = e.subject_code
        INNER JOIN instructor AS i
        ON i.instructor_id=su.instructor_id
        WHERE e.state = "Requested" 
        OR e.state ="Approved"
        GROUP BY e.subject_code');

        return response()->Json($query);
    }

    public function getStudentsRequests()
    {
        $query = DB::select('SELECT DISTINCT e.student_id, sub.user_name , sub.subject_names AS requested_subjects
        FROM enrolment AS e
        INNER JOIN (
            SELECT student_id, GROUP_CONCAT(subject_name SEPARATOR " - ") AS subject_names , user_name
            FROM (
                SELECT e.student_id, su.subject_name , u.user_name
                FROM enrolment AS e
                INNER JOIN subject AS su ON su.subject_code = e.subject_code
                INNER JOIN student AS st ON st.student_id = e.student_id
                INNER JOIN user AS u ON u.user_id = st.user_id
                WHERE e.state = "Requested" OR e.state = "Approved"
                GROUP BY e.student_id, su.subject_code
            ) subjects_per_student
            GROUP BY student_id
        ) sub ON e.student_id = sub.student_id;');
        return response()->Json($query);
    }


    public function handelRequest(request $request)
    {
        $data = $request->input('data');
        foreach ($data as $row) {
            $studentId = $row['student_id'];
            $subjectCode = $row['subject_code'];
            $enrolmentState = $row['state'];

            $trialsubjectName = DB::select('SELECT subject_name
           FROM subject
           WHERE subject_code = :subjecCode', ['subjecCode' => $subjectCode])[0]->subject_name;

            $notificationController = new NotificationController();
            $notificationController->addStudentNotification($studentId, 'Your Request For Subject ' . $trialsubjectName . ' is ' . $enrolmentState);
            // Add each row to the insert data array

            DB::table('enrolment')
                ->where('student_id', $studentId)
                ->where('subject_code', $subjectCode)
                ->update(['state' => $enrolmentState]);
        }
        return response()->json(['message' => 'Data updated successfully']);
    }


    public function studentData($student_id)
    {
        $firstBOX = DB::select('SELECT us.user_name, st.national_id, st.telephone, st.accepted_hours, st.passed_subjects, st.student_level, st.college_state, st.student_gpa
        FROM user AS us
        JOIN student AS st
        ON st.user_id=us.user_id
        WHERE st.student_id =:student_id', ['student_id' => $student_id]);

        $secondBOX = DB::select('SELECT en.semester, su.subject_name, en.classwork, en.final, en.score, en.grade, en.year, en.exam_state, su.subject_code, e.state
               FROM student AS st
               JOIN enrolment AS en
               ON en.student_id=st.student_id
               JOIN subject AS su
               ON en.subject_code = su.subject_code
               WHERE en.student_id =:student_id', ['student_id' => $student_id]);

        $data = [
            'personalData' => $firstBOX,
            'subjectsData' => $secondBOX,
        ];

        return response()->json($data);
    }

    public function updateData(Request $request)
    {
        // Extract personal data
        $data = $request->input('data');
        foreach ($data as $row) {
            $studenID = $row['student_id'];
            $passedSubjects = $row['passed_subjects'];
            $acceptedHours = $row['accepted_hours'];
            $colegeState = $row['college_state'];
            $studentGPA = $row['student_gpa'];

            // Extract subjects data
            // Update personal data using the query builder
            DB::table('student')
                ->where('student_id', $studenID)
                ->update([
                    'accepted_hours' => $acceptedHours,
                    'passed_subjects' => $passedSubjects,
                    'college_state' => $colegeState,
                    'student_gpa' => $studentGPA
                ]);
        }
        // Update subjects data using the query builder

        // Return a JSON response indicating success
        return response()->json(['message' => 'Data updated successfully']);

    }

    public function getGradesTableData($subject_code, $semester, $year)
    {
        $data = DB::select('SELECT  su.subject_name, us.user_name, en.student_id,  en.grade, en.score, en.classwork, en.final, en.exam_state ,su.subject_code, en.state, st.student_gpa
      FROM enrolment AS en
      INNER JOIN student AS st
      ON st.student_id=en.student_id
      INNER JOIN user AS us
      ON us.user_id=st.user_id
      INNER JOIN subject AS su
      ON su.subject_code =en.subject_code
      WHERE en.subject_code = :subjecCode
      AND en.semester = :semester
      AND en.year = :year', ['subjecCode' => $subject_code, 'semester' => $semester, 'year' => $year]);

        return response()->json($data);


    }

    public function getRequestedOrApproved($student_id)
    {
        $Request = DB::select('SELECT   su.subject_code, su.subject_name , su.subject_hours 
        FROM enrolment AS e 
        INNER JOIN subject AS su
        ON su.subject_code = e.subject_code
        WHERE e.state = "Requested"
        AND e.student_id =:student_id', ['student_id' => $student_id]);

        return response()->Json($Request);
    }






}