<?php

use App\User;
use App\UserToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
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
    Cookie::queue('mobile', $request->get('mobile'), 3600);
    return Socialite::driver('facebook')
        ->scopes(['email', 'user_likes',])
        ->redirect();
})->name('fb-redirect');

Route::get('/fb/login', function (Request $request) {
    $u = Socialite::driver('facebook')
        ->user();

    if (!$u) {
        abort(500, 'Wrong user info');
    }

    $facebookId = $u->id;
    $name = $u->name;

    $user = User::where('fb_id', $facebookId)->first();

    if (!$user) {
        $user = User::create([
            'name' => $name,
            'password' => Hash::make(rand() . $facebookId . rand()),
            'fb_id' => $facebookId,
        ]);
    }

    if (!$user) {
        abort(500, 'Cannot save user information');
    }

    $generateToken = function ($rand = null) {
        $generateKey = function ($rand = null) {
            return hash(
                'sha256',
                Crypt::encrypt(
                    $rand . microtime() . '_user_token_' . rand()
                )
            );
        };
        return $generateKey($rand) . $generateKey(rand());
    };

    $tokenKey = $generateToken(rand());

    $token = UserToken::create([
        'user_id' => $user->id,
        'token' => $tokenKey,
    ]);

    $redirectUrl = '/';
    if ($request->cookie('mobile')) {
        $redirectUrl = 'ShoZaSong://login?api_token=' . $token->token;
    }

    return redirect($redirectUrl)->cookie('api_token', $token->token, 3600 * 1000);
})->name('fb-login');

Route::get('/logout', function (Request $request) {
})->name('logout');
