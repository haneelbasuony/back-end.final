<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ExcelImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $trialCount = DB::select('SELECT COUNT(student_id) As count FROM enrolment
            WHERE student_id = :id AND subject_code= :subjectCode', ['id' => $row['student_id'], 'subjectCode' => $row['subject_code']]);

            $count = $trialCount[0]->count;
            // Use the query builder to insert the row data into the database
            DB::table('enrolment')->insert([
                'student_id' => $row['student_id'],
                'subject_code' => $row['subject_code'],
                'trial' => $count,
                'grade' => $row['grade'],
                'score' => $row['score'],
                'state' => $row['state'],
                'classwork' => $row['classwork'],
                'final' => $row['final'],
                'semester' => $row['semester'],
                'year' => $row['year'],
                'exam_state' => $row['exam_state'],
                // Add more columns as needed
            ]);
        }
    }
}