<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FormController;
use App\Http\Controllers\FormBuilderController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can it register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('form-builder');
});
Route::get('/get-form-data', [FormBuilderController::class, 'getFormData'])->name('get.form.data');
Route::post('/save-form', [FormBuilderController::class, 'saveForm'])->name('save.form');
