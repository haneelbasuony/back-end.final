<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class GraphController extends Controller
{
    public function graphBySubject($subject_code, $year, $semester)
    {

        $scoreMapping = [
            "A+" => "Aplus",
            "A" => "A",
            "A-" => "Aminus",
            "B+" => "Bplus",
            "B" => "B",
            "B-" => "Bminus",
            "C+" => "Cplus",
            "C" => "C",
            "C-" => "Cminus",
            "D+" => "Dplus",
            "D" => "D",
            "D-" => "Dminus",
            "F" => "F",
        ];

        $graph = DB::select('SELECT all_scores.score AS score, COUNT(enrolment.score) AS count
        FROM (
            SELECT "A+" AS score UNION ALL
            SELECT "A" UNION ALL
            SELECT "A-" UNION ALL
            SELECT "B+" UNION ALL
            SELECT "B" UNION ALL
            SELECT "B-" UNION ALL
            SELECT "C+" UNION ALL
            SELECT "C" UNION ALL
            SELECT "C-" UNION ALL
            SELECT "D+" UNION ALL
            SELECT "D" UNION ALL
            SELECT "D-" UNION ALL
            SELECT "F"
        ) all_scores
        LEFT JOIN enrolment
            ON all_scores.score = enrolment.score
            AND enrolment.subject_code = ?
            AND enrolment.year = ?
            AND enrolment.semester = ?
        GROUP BY all_scores.score',
            [
                $subject_code,
                $year,
                $semester
            ]
        );
        $result1 = [];
        foreach ($graph as $row) {
            $scoreWord = $scoreMapping[$row->score];
            $result1[$scoreWord] = $row->count;
        }


        $regestrationCount = DB::select('SELECT  COUNT(*) as Total
     FROM enrolment
     WHERE score IN ("A+", "A", "A-", "B+", "B", "B-", "C+", "C", "C-", "D+", "D", "D-", "F")
         AND subject_code = :subjectCode
         AND year = :Year
         AND semester = :Semester',
            [
                'subjectCode' => $subject_code,
                'Year' => $year,
                'Semester' => $semester
            ]
        )[0]->Total;


        $results = DB::select('SELECT (
            SELECT COUNT(*) as PassedStudents
            FROM enrolment
            WHERE grade > 59
                AND subject_code = ?
                AND year = ?
                AND semester = ?
            ) as PassedStudents,
            i.instructor_name,
            s.assistant
            FROM subject AS s
            INNER JOIN instructor AS i
            ON i.instructor_id = s.instructor_id
            WHERE s.subject_code = ?',
            [
                $subject_code,
                $year,
                $semester,
                $subject_code
            ]
        );
        $passedStudents = $results[0]->PassedStudents;
        $instructorName = $results[0]->instructor_name;
        $assistant = $results[0]->assistant;

        $Fulldata = [
            'InstructorName' => $instructorName,
            'AssistantName' => $assistant,
            'StudentCount' => $regestrationCount,
            'PassedStudentsCount' => $passedStudents,
            ...$result1
        ];

        return response()->json($Fulldata);

    }


    public function graphByStudents($year, $semester)
    {
        $ranking = DB::select('SELECT ROW_NUMBER() OVER (ORDER BY st.student_gpa DESC) AS Rank,
        st.student_id,
        us.user_name,
        st.student_gpa
    FROM student AS st
    INNER JOIN user AS us ON us.user_id = st.user_id
    WHERE st.college_state = "Graduated"
   AND st.graduation_semester = ?
   AND st.graduation_year = ?
    ORDER BY st.student_gpa DESC',
            [
                $semester,
                $year

            ]
        );
        return response()->json($ranking);

    }



}