<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function getStatus()
    {
        $regestirationStatus = DB::select('SELECT Distinct
        submition,
        dropablitiy
        FROM subject');

        $subjectStatus = DB::select('SELECT
                subject_code,
                subject_name,
                subject_hours,
                subject_level,
                Term,
                status
                FROM subject');
        $data = [
            'regestirationStatus' => $regestirationStatus,
            'subjectStatus' => $subjectStatus
        ];

        return response()->Json($data);
    }


    public function setSubjectStatus(Request $request)
    {
        $data = $request->input('data');
        foreach ($data as $row) {
            $subjectCode = $row['subject_code'];
            $subjectStatus = $row['status'];
            // Add each row to the insert data array
            DB::table('subject')
                ->where('subject_code', $subjectCode)
                ->update(['status' => $subjectStatus]);
        }


        return response()->json(['message' => 'Data updated successfully']);

    }

    public function setRegestrationStatus($submition, $dropablitiy)
    {

        DB::table('subject')
            ->update([
                'submition' => $submition,
                'dropablitiy' => $dropablitiy
            ]);

        return response()->json(['message' => 'Data updated successfully']);

    }



}