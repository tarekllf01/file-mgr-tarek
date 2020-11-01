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

use App\Http\Controllers\FileManagerController;
use App\Http\Controllers\HomeController;
use App\Models\Settings;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/install',function () {

    Artisan::call('migrate');
    Artisan::call('storage:link');
    return redirect('/login');
});
Auth::routes(['register'=>false]);

Route::get('/home', 'HomeController@index')->name('home');


Route::prefix('/settings')->name('settings.')->group(function(){

    Route::get('/',[ HomeController::class , 'settings'])->name('main');
    Route::get('/allowed-types',[ HomeController::class , 'allowedTypes'])->name('allowedtype');
    Route::post('/save',[ HomeController::class , 'saveSettings'])->name('save');
});

Route::prefix('/files')->name('files.')->group(function(){
    Route::get('/manager',[FileManagerController::class,'index'])->name('manager');
    Route::get('/rename',[FileManagerController::class,'rename'])->name('rename');
    Route::any('/uploader',[FileManagerController::class,'uploader'])->name('uploader');
    Route::any('/uploaderAjax',[FileManagerController::class,'uploaderAjax'])->name('uploaderAjax');
    Route::get('/delete',[ FileManagerController::class , 'deleteFile'])->name('delete');

});


