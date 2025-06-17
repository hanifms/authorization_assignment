<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TodoController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\TwoFactorChallengeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('home');
    }
    return redirect()->route('login');
});

Auth::routes();

// Home controller redirects users based on role
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Todo routes with permission middleware
Route::middleware('auth')->group(function () {
    // Todo listing (requires Retrieve permission)
    Route::get('/todo', [TodoController::class, 'index'])
        ->middleware('permission:Retrieve')
        ->name('todo.index');

    // Todo creation (requires Create permission)
    Route::get('/todo/create', [TodoController::class, 'create'])
        ->middleware('permission:Create')
        ->name('todo.create');
    Route::post('/todo', [TodoController::class, 'store'])
        ->middleware('permission:Create')
        ->name('todo.store');

    // Todo viewing (requires Retrieve permission)
    Route::get('/todo/{todo}', [TodoController::class, 'show'])
        ->middleware('permission:Retrieve')
        ->name('todo.show');

    // Todo editing (requires Update permission)
    Route::get('/todo/{todo}/edit', [TodoController::class, 'edit'])
        ->middleware('permission:Update')
        ->name('todo.edit');
    Route::put('/todo/{todo}', [TodoController::class, 'update'])
        ->middleware('permission:Update')
        ->name('todo.update');

    // Todo deletion (requires Delete permission)
    Route::delete('/todo/{todo}', [TodoController::class, 'destroy'])
        ->middleware('permission:Delete')
        ->name('todo.destroy');
});

// User profile routes
Route::middleware(['auth'])->group(function () {
    Route::post('/user/two-factor-authentication', [App\Http\Controllers\TwoFactorController::class, 'enableTwoFactor'])
        ->name('two-factor.enable');

    Route::delete('/user/two-factor-authentication', [App\Http\Controllers\TwoFactorController::class, 'disableTwoFactor'])
        ->name('two-factor.disable');

    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile/two-factor', [ProfileController::class, 'enableTwoFactor'])->name('profile.two-factor.enable');
    Route::delete('/profile/two-factor', [ProfileController::class, 'disableTwoFactor'])->name('profile.two-factor.disable');
});

// Admin routes protected by role middleware
Route::middleware(['auth', 'role:Administrator'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/users/{id}/todos', [AdminController::class, 'userTodos'])->name('user.todos');
    Route::post('/users/{id}/toggle', [AdminController::class, 'toggleActivation'])->name('user.toggle');
    Route::delete('/users/{id}', [AdminController::class, 'deleteUser'])->name('user.delete');
    Route::get('/users/{id}/permissions', [AdminController::class, 'managePermissions'])->name('permissions');
    Route::post('/users/{id}/permissions', [AdminController::class, 'updatePermissions'])->name('permissions.update');
});

// Two-factor authentication routes
Route::middleware(['guest'])->group(function () {
    Route::get('/two-factor-challenge', [TwoFactorChallengeController::class, 'create'])
        ->name('2fa.challenge');

    Route::post('/two-factor-challenge', [TwoFactorChallengeController::class, 'store']);
});

// Fallback route
Route::fallback(function () {
    if (Auth::check()) {
        return redirect()->route('home');
    }
    return redirect()->route('login');
});
