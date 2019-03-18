<?php

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

Route::get('/', 'UsersController@index')->name('home');
Route::get('/error', 'UsersController@getErrorView')->name('error');

Route::prefix('admin')->group(function(){
	// Route::get('/', 'AdminController@index');
	Route::get('/exam/results', 'AdminController@getExamResults');
	Route::get('/exam/results/{id}', 'AdminController@getEmployeeResults');
	// Route::get('/form/list', 'AdminController@getNewHireList');
	// Route::get('/form/data/{id}', 'AdminController@getNewHireData');
	// Route::get('/form/data/{id}/generate', 'AdminController@generateData');
});


Route::prefix('exam')->group(function(){
	Route::get('/', 'ExamController@index');
	Route::post('/submit', 'ExamController@submit')->name('submitExam');
	Route::post('/start', 'ExamController@start')->name('startExam');
	Route::get('/generate', 'ExamController@generate')->name('generate');
	Route::get('/resume', 'ExamController@resume')->name('resumeExam');
	Route::get('/complete', 'ExamController@complete');
	Route::get('/flush', 'ExamController@flush');
	Route::get('/lang', 'ExamController@changeLanguage')->name('changeLang');
});

// Route::get('/google', function(){
// 	return view('google');
// });

Route::get('/success', function(){
	return view('success');
})->name('success');