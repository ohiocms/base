<?php

Route::group(['middleware' => ['web']], function () {
    Route::get('/', function () {
        return view('base::front.home');
    });
});

Route::group(['prefix' => 'admin', 'middleware' => ['web', 'auth.admin']],
    function () {
        Route::get('/', \Ohio\Base\Http\Controllers\AdminController::class . '@getIndex');
    }
);

Route::group(['prefix' => 'admin-user', 'middleware' => ['web']],
    function () {
        Route::get('/', \Ohio\Base\Http\Controllers\AdminUserController::class . '@getIndex');
    }
);