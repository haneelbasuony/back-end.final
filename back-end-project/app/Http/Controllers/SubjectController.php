<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function getStatus()
    {
        $subjectStatus = DB::select('SELECT
                subject_code,
                subject_name,
                subject_hours,
                subject_level,
                Term,
                status
                FROM subject');
        return response()->Json($subjectStatus);
    }
    public function setStatus($subjectId, $subjectStatus)
    {
     DB::table('subject')
        ->where('subject_code', $subjectId)
        ->update(['status' => $subjectStatus]);
        return response()->Json('update is done');

    }
}
