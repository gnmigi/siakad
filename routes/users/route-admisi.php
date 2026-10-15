<?php

use Illuminate\Support\Facades\Route;

// HAK AKSES DEPARTEMENT ADMISI
Route::group(['prefix' => 'admisi', 'middleware' => ['checkUser:Departement Admisi'], 'as' => 'admisi.'], function() {
    // GLOBAL ROUTE
    require __DIR__.'/../private-core.php';

    // MASTER AUTHORITY
    require __DIR__.'/../master-core.php';
});
