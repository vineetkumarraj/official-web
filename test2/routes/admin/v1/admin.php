<?php

use Illuminate\Support\Facades\Route;

# Testing the admin api url
Route::get('/', function () {
    return ['admin' => 'api is beeing tested'];
});
