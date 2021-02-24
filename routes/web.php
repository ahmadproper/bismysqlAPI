<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//root



//routes
// Route::get('/', function () {
//     return view('index'); 
     
// });

// //routes
// Route::get('/about', function () {
//     $nama = 'Ahmad Basori';
//     return view('about', ['nama'=> $nama]);
// });

Route::get('/','welcome@index');
Route::get('/about','PagesController@about');
Route::get('/mahasiswa','MahasiswaController@index');
Route::get('/odoo','ApiOdooController@home');
Route::get('/bisgateway','PurchaseOrderController@index');
#students
//Route::get('/students','StudentsController@index');
//Route::get('/students/create','StudentsController@create');
//Route::get('/students/{student}','StudentsController@show');
//Route::post('/students','StudentsController@store');
//Route::delete('/students/{student}','StudentsController@destroy');
//Route::get('/students/{student}/edit','StudentsController@edit');
//Route::patch('/students/{student}','StudentsController@update');

Route::resource('students','StudentsController');

//test data pipeline
Route::get('/jenkinstest', 'PurchaseOrderController@jenkins');