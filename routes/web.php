<?php

use App\Http\Controllers\JwtEndpoint;
use App\Http\Controllers\OnLogin;
use App\Http\Controllers\OnSignup;
use Illuminate\Support\Facades\Route;

Route::get("/", function () {

    return view('index');
}); 

Route::post("/login", [OnLogin::class, "login"]);

Route::post("/signup", [OnSignup::class, "signup"]);

Route::post("/token", [JwtEndpoint::class, "generate"]);