<?php

use Illuminate\Support\Facades\Route;

// HAK AKSES DEPARTEMENT KEMAHASISWAAN
Route::group(['prefix' => 'kemahasiswaan', 'middleware' => ['checkUser:Departement Kemahasiswaan'], 'as' => 'kemahasiswaan.'], function() {
    // GLOBAL ROUTE
    require __DIR__.'/../private-core.php';

    // MASTER AUTHORITY
    require __DIR__.'/../master-core.php';
});
