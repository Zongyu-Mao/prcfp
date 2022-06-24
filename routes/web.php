<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestController;

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

Route::get('/', function () {
    return view('welcome');
});

Route::get('test', [TestController::class, 'test']);

Route::any('/home/test', function () {
    echo 'welcome';
});

Route::any('/home/test1', function () {
    echo 'welcome';
});

Route::get('hello', function () {
    return 'Hello Laravel!';
});

Route::get('hello11', function () {
    return 'Hello Laravel!';
});