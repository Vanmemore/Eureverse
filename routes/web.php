<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Models\User;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\FollowController;


// -----------------------------
// Halaman Umum
// -----------------------------

Route::get('/', fn() => view('welcome'))->name('welcome');
Route::get('/about', fn() => view('about'))->name('about');
Route::get('/search', [SearchController::class, 'index'])->name('search');
// -----------------------------
// Halaman Home
// -----------------------------

Route::get('/home', [PostController::class, 'index'])->name('home');

// -----------------------------
// Auth
// -----------------------------

Route::get('/register', [RegisterController::class, 'show'])->name('register');
Route::post('/register', [RegisterController::class, 'register'])->name('register.submit');

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// -----------------------------
// Profil (harus login)
// -----------------------------

Route::middleware('auth')->group(function () {
    Route::get('/profile', [UserController::class, 'profile'])->name('profile');
    Route::get('/profile/edit', [UserController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/update', [UserController::class, 'update'])->name('profile.update');
    Route::post('/posts/{id}/comments', [CommentController::class, 'store'])->name('comments.store');
});

// -----------------------------
// Postingan (harus login)
// -----------------------------

Route::middleware('auth')->group(function () {
    Route::resource('posts', PostController::class)->except(['show']);
});

// -----------------------------
// Foto Profil dari Database
// -----------------------------

Route::get('/user/photo/{id}', [UserController::class, 'photo'])->name('user.photo');

Route::middleware('auth')->group(function () {
    Route::resource('posts', PostController::class)->except(['show']);
    Route::post('/comments/{comment}/like', [CommentController::class, 'like'])->name('comments.like');
    Route::put('/comments/{comment}', [CommentController::class, 'update'])->name('comments.update');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
    Route::post('/posts/{post}/like', [PostController::class, 'like'])->name('posts.like');
});
Route::resource('posts', PostController::class)->only(['store', 'update', 'destroy']);
Route::get('/posts/{id}', [PostController::class, 'show'])->name('posts.show');

// -----------------------------

// web.php
Route::get('/search', [SearchController::class, 'index'])->name('search');
Route::get('/search/users', [SearchController::class, 'searchUsers'])->name('search.users');
Route::post('/follow/{user}', [FollowController::class, 'store'])->middleware('auth')->name('follow');
Route::delete('/unfollow/{user}', [FollowController::class, 'destroy'])->middleware('auth')->name('unfollow');


// -----------------------------
