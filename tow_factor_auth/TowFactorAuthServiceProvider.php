<?php


namespace TowFactorAuth;


use Database\Factories\UserFactory;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use TowFactorAuth\Authenticator\SessionAuth;
use TowFactorAuth\Facades\AuthFacade;
use TowFactorAuth\Facades\TokenGeneratorFacade;
use TowFactorAuth\Facades\TokenSenderFacade;
use TowFactorAuth\Facades\TokenStoreFacade;
use TowFactorAuth\Facades\UserProviderFacade;
use TowFactorAuth\Http\ResponderFacade;
use TowFactorAuth\Http\Responses\VueResponses;
use TowFactorAuth\TokenGenerator\FakeTokenGenerator;
use TowFactorAuth\TokenGenerator\TokenGenerator;
use TowFactorAuth\TokenSender\FakeTokenSender;
use TowFactorAuth\TokenSender\TokenSender;
use TowFactorAuth\TokenStore\FakeTokenStore;
use TowFactorAuth\TokenStore\TokenStore;

class TowFactorAuthServiceProvider extends ServiceProvider
{
    private $namespace='TwoFactorAuth/Http/Controllers';

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/config/tow_factor_auth_config.php',
        'tow_factor_config'
        );


        AuthFacade::shouldProxyTo(SessionAuth::class);
        if (app()->runningUnitTests()){
            $tokenGenerator=FakeTokenGenerator::class;
            $tokenStore=FakeTokenStore::class;
            $tokenSender=FakeTokenSender::class;
            $isBanned=FakeUserProvider::class;
        }else{
            $tokenGenerator=TokenGenerator::class;
            $tokenStore=TokenStore::class;
            $tokenSender=TokenSender::class;
            $isBanned=UserFactory::class;
        }
        TokenGeneratorFacade::shouldProxyTo($tokenGenerator);
        TokenStoreFacade::shouldProxyTo($tokenStore);
        TokenSenderFacade::shouldProxyTo($tokenSender);
        UserProviderFacade::shouldProxyTo($isBanned);


    }

    public function boot()
    {
        if (!$this->app->routesAreCached()){
            $this->defineRoutes();
        }

    }

    private function defineRoutes()
    {
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(__DIR__ . './routes.php');
    }

}
