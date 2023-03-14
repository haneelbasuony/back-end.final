<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\GradesController;
use App\Http\Controllers\StudentController;
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

/*Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});*/


// View All Users Data
Route::get('/user_index', [UserController::class, 'Index']);

//Authonication
Route::get('/auth/{mail}/{password}', [UserController::class, 'Find_User']);

//Show Data(leveName,l,GPA)
Route::get('/data/{id}',[StudentController::class , 'getData']);
//Image
Route::get('/image/{id}', [StudntController::class, 'image']);


//Show Grades
Route::get('/grades/{id}', [GradesController::class, 'All_Grades']);

//Show Grades For certian level
Route::get('/grades/{id}/{level}', [GradesController::class, 'gradesLevel']);

//Show Subject Data only (code,name,creditHour,state)
//Route::get('/subject/{id}/{level}/{term}',[]);

//Show 



//Show user Image
//Route::get('/image/{id}', [StudentController::class, 'getImage']);
