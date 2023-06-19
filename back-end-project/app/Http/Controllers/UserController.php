<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * Get all users' names, email addresses, and passwords.
     *
     * @return Illuminate\Support\Collection A collection of user records.
     */
    public function Index()
    {
        $user = DB::table('user')
            ->select('user_name', 'user_email', 'user_password')
            ->get();
        return $user;
    }

    /**
     * Find a registered user with the given email address and password.
     *
     * @param string $mail The email address of the user to find.
     * @param string $password The password of the user to find.
     * @return Illuminate\Http\JsonResponse A JSON response with the student ID if a registered user is found, or an error message if not.
     */
    public function Find_User($mail, $password)
    {
        if (DB::table('user')
            ->where('user_email', $mail)
            ->where('user_password', $password)
            ->exists()
        ) {
            // Retrieve the student ID of the registered user with the given email address
            if (DB::table('user')
                ->where('user_email', $mail)
                ->where('user_password', $password)
                ->where('priv_id', '=', 1)
                ->exists()
            ) {
                $student_id = DB::select('SELECT student_id
        FROM student
        INNER JOIN user
        ON student.user_id = user.user_id
        WHERE user_email=:mail', ['mail' => $mail]);
                $studentData = DB::select('SELECT st.student_id, u.user_name ,st.student_level,st.student_gpa,u.priv_id
         FROM student AS st
         INNER JOIN user AS u ON st.user_id =u.user_id
         WHERE st.student_id =:id ', ['id' => $student_id[0]->student_id]);

                return response()->json($studentData);
            }

            if (DB::table('user')
                ->where('user_email', $mail)
                ->where('user_password', $password)
                ->where('priv_id', '=', 2)
                ->exists()
            ) {
                $advisorData = DB::select('SELECT a.advisor_id ,u.user_name,u.priv_id
                FROM advisor As a
                INNER JOIN user As u
                ON a.user_id = u.user_id
                WHERE u.user_email=:mail', ['mail' => $mail]);

                return response()->json($advisorData);
            }
        }

        // Return an error message if a registered user is not found
        if (DB::table('user')
            ->where('user_email', $mail)
            ->where('user_password', $password)
            ->doesntExist()
        ) {
            return response()->Json("Not A Registered User GO BACK");
        }
    }
}
