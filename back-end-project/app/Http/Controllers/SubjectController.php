<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function getStatus()
    {
        $regestrationStatus = DB::select('SELECT distinct
                submition,
                dropablitiy
                FROM subject     
            ');
                
        $subjectStatus = DB::select('SELECT
                subject_code,
                subject_name,
                subject_hours,
                subject_level,
                Term,
                status,
                submition,
                dropablitiy
                FROM subject');
        return response()->Json($subjectStatus);
    }


    public function setStatus(Request $request)
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




}