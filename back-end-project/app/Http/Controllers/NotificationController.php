<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    public function addAdvisorNotification($advisor_id, $message)
    {
        DB::table('advisor_notfication')->insert([
            'advisor_id' => $advisor_id,
            'message' => $message,
        ]);
    }

    public function getAdvisorNotification($advisor_id)
    {
        $notfication = DB::select('SELECT message
       FROM advisor_notfication    
       WHERE advisor_id = :id
       AND read_info = "Not Read" ', ['id' => $advisor_id]);

        DB::table('advisor_notfication')
            ->where('advisor_id', '=', $advisor_id)
            ->update(['read_info' => 'Read']);

        return response()->json($notfication);
    }

    public function addStudentNotification($studentId, $message)
    {
        DB::table('student_notfications')->insert([
            'student_id' => $studentId,
            'message' => $message,
        ]);
    }

    public function getStudentNotification($studentId)
    {
        $notfication = DB::select('SELECT message
        FROM student_notfications   
        WHERE student_id = :id
        AND read_info = "Not Read" ', ['id' => $studentId]);
 
         DB::table('student_notfications')
             ->where('student_id', '=', $studentId)
             ->update(['read_info' => 'Read']);
 
         return response()->json($notfication);
    }

}