<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers as Controllers;

Route::middleware('logged-user')->group(function(){
    $url='columns-control';
    $controllerClass=Controllers\columns_control\ColumnsControlController::class;
    // Route::match(array('GET','POST'),$url.'/initialize', [$controllerClass, 'initialize']);
    // Route::match(array('GET','POST'),$url.'/get-items', [$controllerClass, 'getItems']);
    // Route::match(array('GET','POST'),$url.'/get-item/{itemId}', [$controllerClass, 'getItem']);
    Route::post($url.'/save-item', [$controllerClass, 'saveItem']);    
});