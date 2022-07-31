<?php

use App\Http\Controllers\CoursController;
use App\Http\Controllers\FormationController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\PlanningController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\UserController;
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

Route::redirect('/',        'login');

Route::get('home',          [HomeController::class, 'index'])->name('home');

Route::get('register',      [RegisterController::class, 'index'])->name('register');

Route::post('register',     [RegisterController::class, 'register'])->name('register');

Route::get('login',         function () { return view('login'); })->name('login');

Route::post('login',        [LoginController::class, 'authenticate'])->name('login');

Route::post('logout',       [LoginController::class, 'logout'])->name('logout');

// =====

Route::resource('users',        UserController::class);

Route::resource('cours',        CoursController::class);

Route::resource('formations',   FormationController::class);

Route::resource('plannings',    PlanningController::class);

// =====

Route::get('ma-formation',                  [FormationController::class,    'maFormation']);

Route::get('inscription-cours/{cours}',     [CoursController::class,        'inscription']);

Route::post('inscription-cours',            [CoursController::class,        'storeInscription']);

Route::post('desinscription/{cours}',       [CoursController::class,        'desinscription'])->name('desinscription');

Route::get('mes-cours',                     [CoursController::class,        'mesCours']);

Route::get('recherche-cours',               [CoursController::class,        'rechercheCours']);

Route::get('mon-planning',                  [PlanningController::class,     'monPlanning']);

Route::get('recherche-utilisateur',         [UserController::class,         'rechercheUser']);

Route::get('cours-par-enseignant',          [CoursController::class,        'coursParEnseignant']);

Route::post('refuserInscription/{user}',    [UserController::class,        'refuserInscription'])->name('refuserInscription');
