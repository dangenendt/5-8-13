<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->make(null, 204);
});
