<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;


use Illuminate\Http\Request;

class AdvisorController extends Controller
{

    public function getImage($id)
    {
        // Retrieve the profile image and MIME type of the student with the given ID
        $image = DB::table('advisor')->select('data', 'mime_type')->where('advisor_id', $id)->first();

        // Return a response with the student's profile image and MIME type
        return response($image->data, 200)->header('Content-Type', $image->mime_type);
    }


}
