<?php

use Illuminate\Support\Facades\Route;

// HAK AKSES DEPARTEMENT PERPUSTAKAAN
Route::group(['prefix' => 'library', 'middleware' => ['checkUser:Departement Perpustakaan'], 'as' => 'library.'], function() {
    // GLOBAL ROUTE
    require __DIR__.'/../private-core.php';

    // MASTER AUTHORITY
    require __DIR__.'/../master-core.php';
});
