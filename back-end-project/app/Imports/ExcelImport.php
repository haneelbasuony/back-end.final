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
            // Use the query builder to insert the row data into the database
            DB::table('enrolment')->insert([
                'student_id' => $row['student_id'],
                'subject_code' => $row['subject_code'],
                'grade' => $row['grade'],
                'score' => $row['score'],
                'state' => $row['state'],
                // Add more columns as needed
            ]);
        }
    }
}
