<?php

use Illuminate\Support\Facades\Route;

// HAK AKSES DEPARTEMENT KEUANGAN
Route::group(['prefix' => 'finance', 'middleware' => ['checkUser:Departement Keuangan'], 'as' => 'finance.'], function() {
    // GLOBAL ROUTE
    require __DIR__.'/../private-core.php';

    // MASTER AUTHORITY
    require __DIR__.'/../master-core.php';
});
