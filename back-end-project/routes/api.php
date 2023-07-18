<?php

use App\Http\Controllers\NotificationController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\GradesController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\AdvisorController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\GraphController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Comment: This is a comment for the default route generated by Laravel, which is currently commented out.
/*Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});*/


//-------User----------------------------
//----------------------------------------
// Comment: This route is for getting all users' data.
//Route::get('/user_index', [UserController::class, 'Index']);

// Comment: This route is for authenticating a user by email and password.
Route::get('/auth/{mail}/{password}', [UserController::class, 'Find_User']);




//-------Student--------------------------
//----------------------------------------

// Comment: This route is for getting a student's profile image by ID.
Route::get('student/Image/{id}', [StudentController::class, 'getImage']);

// Comment: This route is for getting all grades of a student by ID.
Route::get('student/grades/{id}', [GradesController::class, 'All_Grades']);

// Comment: This route is for getting all grades of a student for a certain level by ID and level.
Route::get('student/grades/{id}/{level}', [GradesController::class, 'gradesLevel']);

// Comment: This route is for getting the subject data (code, name, credit hour, state) for a student's enrollment in a certain term, identified by ID, level, and term.
Route::get('student/subject/{id}', [EnrollmentController::class, 'termState']);

//Insert Enrolment OK
Route::post('student/enrolmentState', [EnrollmentController::class, 'enrolmentState']);

// student Notfication Menu
Route::get('student/getnotfication/{studentID}', [NotificationController::class, 'getStudentNotification']);

//Show request student requested subjects 
Route::get('student/enrolment/getRequests/{id}', [EnrollmentController::class, 'getRequestedOrApproved']);


//-------Advisor--------------------------
//----------------------------------------
// Get advisor image (OK)
Route::get('advisor/Image/{id}', [AdvisorController::class, 'getImage']);

//Show request (waiting GUI)
Route::get('advisor/enrolment/getRequest', [EnrollmentController::class, 'getRequest']);

//handelRequest (waiting GUI)
Route::post('advisor/enrolment/handelRequest', [EnrollmentController::class, 'handelRequest']);

// insert grades or update for each student (OK)
Route::put('advisor/studentGrades/insertGrade', [EnrollmentController::class, 'setGrade']);

//advisor get studnent data (OK)
Route::get('advisor/studentData/{studentID}', [EnrollmentController::class, 'studentData']);

//update student table (OK)
Route::put('advisor/insertPersonal', [EnrollmentController::class, 'updateData']);

// Upload XLS (OK)
Route::post('advisor/uploadCSV', [GradesController::class, 'importExcel']);

// get students of a subject in a certain semester and a year
Route::get('advisor/subjectGrades/getGrades/{subjectCode}/{semester}/{year}', [EnrollmentController::class, 'getGradesTableData']);

// ADvisor Notfication Menu
Route::get('advisor/getnotfication/{advisorId}', [NotificationController::class, 'getAdvisorNotification']);




//-------------Subject--------------------
//get all subject status(open or closed)
Route::get('advisor/subject/subjectStatus', [SubjectController::class, 'getStatus']);

//post request to change subject state (open or closed) for advisor
Route::post('/subject/updateRegestrationStatus/{submition}/{dropability}/{year}/{semester}', [SubjectController::class, 'setRegestrationStatus']);


Route::post('/subject/update/SubjectStatus', [SubjectController::class, 'setSubjectStatus']);


// ---------------Graph--------------
//---------------------------------------

//grap data and statistics about each subject
Route::get('graph/bySubject/{subject_code}/{year}/{semester}', [GraphController::class, 'graphBySubject']);

//statisctics about graduated studients in a certain semester
Route::get('graph/byStudents/{year}/{semester}', [GraphController::class, 'graphByStudents']);