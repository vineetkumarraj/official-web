<?php

use Illuminate\Support\Facades\Route;

# Testing the user api url
Route::get('/', function () {
    return ['user' => 'api is being tested'];
});
