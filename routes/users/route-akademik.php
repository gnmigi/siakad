<?php

use Illuminate\Support\Facades\Route;

// HAK AKSES DEPARTEMENT AKADEMIK
Route::group(['prefix' => 'akademik', 'middleware' => ['checkUser:Departement Akademik'], 'as' => 'akademik.'], function() {
    // GLOBAL ROUTE
    require __DIR__.'/../private-core.php';

    // MASTER AUTHORITY
    require __DIR__.'/../master-core.php';
});
