<?php

use Illuminate\Support\Facades\Route;

// HAK AKSES DEPARTEMENT INFRASTRUKTUR & IT
Route::group(['prefix' => 'it', 'middleware' => ['checkUser:Departement Infrastruktur & IT'], 'as' => 'it.'], function() {
    // GLOBAL ROUTE
    require __DIR__.'/../private-core.php';

    // MASTER AUTHORITY
    require __DIR__.'/../master-core.php';
});
