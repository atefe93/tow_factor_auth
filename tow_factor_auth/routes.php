<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use TowFactorAuth\Facades\AuthFacade;
use TowFactorAuth\Facades\TokenSenderFacade;
use TowFactorAuth\Facades\TokenStoreFacade;
use TowFactorAuth\Http\Controllers\TokenSenderController;

Route::get('/tow-factor-auth/request-token', [TokenSenderController::class, 'issueToken'])
    ->name('2factor.requestToken');
Route::get('/tow-factor-auth/login', [TokenSenderController::class, 'loginWithToken'])
    ->name('2factor.login');



if (app()->environment('local')) {
    Route::get('test/token-notif', function () {
        User::unguard();
        $data = ['id' => 1, 'email' => 'atefe.boluri@yahoo.com'];
        $user = new User($data);
        TokenSenderFacade::send('123456', $user);
    });
    Route::get('test/token-storage', function () {
        config()->set('tow_factor_config.token_ttl', 3);
        TokenStoreFacade::saveToken('kdkdkd',1);
        sleep(1);
        $uid=TokenStoreFacade::getUidByToken('kdkdkd');
        if ($uid!=1){
            dd('some problem with reading');
        }
        $uid=TokenStoreFacade::getUidByToken('kdkdkd');
        if (!is_null($uid)){
            dd('some problem with reading');
        }
        config()->set('tow_factor_config.token_ttl', 1);
        TokenStoreFacade::saveToken('kdkdkd',1);
        sleep(1.1);
        $uid=TokenStoreFacade::getUidByToken('kdkdkd');
        if (!is_null($uid)){
            dd('some problem with reading');
        }
        dd('catch store seems to be ok');
    });
    Route::get('test/login', function () {

        $user = User::create([
            'name'=>'test',
            'email' => 'atefe.boluri@yahoo.com',
            'password'=>Hash::make('123456789')
        ]);
        AuthFacade::loginById($user->id);
        if (Auth::id()!=$user->id){
            dd('failed');
        }
        $user->delete();
        dd('successful');
    });
}

