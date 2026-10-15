<?php

use Illuminate\Support\Facades\Route;

// HAK AKSES DEPARTEMENT UMUM
Route::group(['prefix' => 'umum', 'middleware' => ['checkUser:Departement Umum'], 'as' => 'umum.'], function() {
    // GLOBAL ROUTE
    require __DIR__.'/../private-core.php';

    // MASTER AUTHORITY
    require __DIR__.'/../master-core.php';
});
