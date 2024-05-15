<?php

use Illuminate\Support\Facades\Route;
use App\http\Controllers\AuthController;
use App\Mail\VerifyEmail;
use App\models\User; 
use App\Events\UserRegistered;
use Illuminate\Support\Str;
use Carbon\Carbon;


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

