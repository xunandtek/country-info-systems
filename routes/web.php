<?php

use App\Http\Controllers\CountryController;
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

Route::get('/', fn () => redirect('/countries'));

Route::get('/countries', [CountryController::class, 'index'])->name('countries.index');
Route::get('/countries/{code}', [CountryController::class, 'show'])->name('countries.show');
// Route::get('/', function () {
//     return view('welcome');
// });
