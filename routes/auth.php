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
});

Route::get('/fb/redirect', function (Request $request) {
    $redirectUrl = route('fb-login');
    //var_dump($redirectUrl);
    return Socialite::driver('facebook')->scopes([
        'email', 'user_likes',
    ])->with(['redirect_uri' => $redirectUrl,])->redirect();
});

Route::get('/fb/login', function (Request $request) {

})->name('fb-login');

Route::get('/logout', function (Request $request) {

});
