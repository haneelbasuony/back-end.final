<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Response;

class StudentController extends Controller
{
    /**
     * Get the name, level, and GPA of a student with the given ID.
     *
     * @param int $id The ID of the student to retrieve data for.
     * @return Illuminate\Http\JsonResponse A JSON response with the student data.
     */
    public function getData($id)
    {
        // Retrieve the name, level, and GPA of the student with the given ID
        $data = DB::select('SELECT u.user_name ,st.student_level,st.student_gpa
        FROM student AS st
        INNER JOIN user AS u ON st.user_id =u.user_id
        WHERE st.student_id =:id ', ['id' => $id]);

        // Return a JSON response with the student data
        return response()->Json($data);
    }

    /**
     * Get the profile image of a student with the given ID.
     *
     * @param int $id The ID of the student to retrieve the image for.
     * @return Illuminate\Http\Response A response with the student's profile image and MIME type.
     */
    public function image($id)
    {
        // Retrieve the profile image and MIME type of the student with the given ID
        $image = DB::table('student')->select('data', 'mime_type')->where('student_id', $id)->first();

        // Return a response with the student's profile image and MIME type
        return response($image->data, 200)->header('Content-Type', $image->mime_type);
    }
}
