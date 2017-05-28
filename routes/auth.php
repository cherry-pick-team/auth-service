<?php

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/

Route::get('/me', function (Request $request) {
    return ['user' => $request->user()];
})->name('user');

Route::get('/fb/redirect', function (Request $request) {
    return Socialite::driver('facebook')
        ->scopes(['email', 'user_likes',])
        ->redirect();
})->name('fb-redirect');

Route::get('/fb/login', function (Request $request) {
    $u = Socialite::driver('facebook')
        ->user();
    var_dump($u);
    exit;
})->name('fb-login');

Route::get('/logout', function (Request $request) {
})->name('logout');
