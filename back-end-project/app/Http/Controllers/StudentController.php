<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Response;

class StudentController extends Controller
{
    public function getImage($id)
    {
        // Retrieve the profile image and MIME type of the student with the given ID
        $image = DB::table('student')->select('data', 'mime_type')->where('student_id', $id)->first();

        // Return a response with the student's profile image and MIME type
        return response($image->data, 200)->header('Content-Type', $image->mime_type);
    }
}
