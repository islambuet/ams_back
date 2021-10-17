<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers as Controllers;

$url='user';
$controllerClass=Controllers\user\UserController::class;

Route::match(array('GET','POST'),$url.'/initialize', [$controllerClass, 'initialize']);
Route::post($url.'/login', [$controllerClass, 'login']);
Route::match(array('GET','POST'),$url.'/logout', [$controllerClass, 'logout']);

Route::middleware('logged-user')->group(function(){
    $url='user';
    $controllerClass=Controllers\user\UserController::class;
    Route::post($url.'/profile-picture', [$controllerClass, 'profilePicture']);
    Route::post($url.'/change-password', [$controllerClass, 'ChangePassword']);
});

