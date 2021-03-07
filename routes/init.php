<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Initialization Routes
|--------------------------------------------------------------------------
*/

if (app()->environment('local')) {
    Route::get('info', function () {
        phpinfo();
    });
}

Route::get('/', function () {
    return 'Hello world';
});
