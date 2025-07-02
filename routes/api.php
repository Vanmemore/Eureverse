<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserApiController;

Route::get('/users', [UserApiController::class, 'welcome']);
Route::get('/users/{id}', [UserApiController::class, 'show']);

