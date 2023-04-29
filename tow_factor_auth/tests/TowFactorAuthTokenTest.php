<?php


namespace TowFactorAuth\tests;


use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;
use TowFactorAuth\Facades\AuthFacade;
use TowFactorAuth\Facades\TokenGeneratorFacade;
use TowFactorAuth\Facades\TokenSenderFacade;
use TowFactorAuth\Facades\TokenStoreFacade;
use TowFactorAuth\Facades\UserProviderFacade;
use TowFactorAuth\Http\ResponderFacade;

class TowFactorAuthTokenTest extends TestCase
{
    public function test_the_happy_path()
    {
       // $this->withoutExceptionHandling();
        User::unguard();
        AuthFacade::shouldReceive('check')
            ->once()
            ->andReturn(false);
        UserProviderFacade::shouldReceive('getUserByEmail')
            ->once()
            ->with('atefe.boluri@yahoo.com')
            ->andReturn($user = new User(['id' => 1, 'email' => 'atefe.boluri@yahoo.com']));
        UserProviderFacade::shouldReceive('isBanned')
            ->once()
            ->with($user)
            ->andReturn(false);

        TokenGeneratorFacade::shouldReceive('generateToken')
            ->once()
            ->withNoArgs()
            ->andReturn('1qw8gf');

        TokenStoreFacade::shouldReceive('saveToken')
            ->once()
            ->with('1qw8gf', $user->id);
        TokenSenderFacade::shouldReceive('send')->once()->with('1qw8gf', $user);
        ResponderFacade::shouldReceive('tokenSent')->once();
        $this->json('GET', 'api/tow-factor-auth/request-token?email=atefe.boluri@yahoo.com', ['Accept' => 'application/json']);


    }

    public function test_android_responses()
    {
       // $this->withoutExceptionHandling();
        User::unguard();

//        UserProviderFacade::shouldReceive('getUserByEmail')
//            ->andReturn($user = new User(['id' => 1, 'email' => 'atefe.boluri@yahoo.com']));
//       UserProviderFacade::shouldReceive('isBanned')->andReturn(false);

        $response = $this->json('GET', 'api/tow-factor-auth/request-token?email=atefe.boluri@yahoo.com&client=android', ['Accept' => 'application/json']);

        $response->assertJson(['msg' => 'token was sent to your app.']);

    }


    public function test_user_is_banned()
    {
        User::unguard();


        UserProviderFacade::shouldReceive('getUserByEmail')
            ->once()
            ->with('baran@yahoo.com')
            ->andReturn($user = new User(['id' => 1, 'email' => 'baran@yahoo.com']));
        UserProviderFacade::shouldReceive('isBanned')
            ->once()
            ->with($user)
            ->andReturn(true);
        TokenGeneratorFacade::shouldReceive('generateToken')
            ->never();

        TokenStoreFacade::shouldReceive('saveToken')
            ->never();
        TokenSenderFacade::shouldReceive('send')->never();

        $response = $this->json('GET', 'api/tow-factor-auth/request-token?email=baran@yahoo.com', ['Accept' => 'application/json']);
        $response->assertStatus(400);
        $response->assertJson(['error' => 'you are blocked']);

    }

    public function test_email_dose_not_exist()
    {

        UserProviderFacade::shouldReceive('getUserByEmail')
            ->once()
            ->with('baran@yahoo.com')
            ->andReturn(null);
        UserProviderFacade::shouldReceive('isBanned')->never();
        TokenGeneratorFacade::shouldReceive('generateToken')->never();
        TokenStoreFacade::shouldReceive('saveToken')->never();
        TokenSenderFacade::shouldReceive('send')->never();
        ResponderFacade::shouldReceive('userNotFound')->once()->andReturn('hello');
        $response = $this->json('GET', 'api/tow-factor-auth/request-token?email=baran@yahoo.com', ['Accept' => 'application/json']);

        $response->assertSee('hello');
    }

    public function test_email_not_valid()
    {
        UserProviderFacade::shouldReceive('getUserByEmail')->never();
        UserProviderFacade::shouldReceive('isBanned')->never();
        TokenGeneratorFacade::shouldReceive('generateToken')->never();
        TokenStoreFacade::shouldReceive('saveToken')->never();
        TokenSenderFacade::shouldReceive('send')->never();
        ResponderFacade::shouldReceive('emailNotValid')->once()->andReturn(response('hello'));
        $response = $this->json('GET', 'api/tow-factor-auth/request-token?email=baran_yahoo.com', ['Accept' => 'application/json']);
        $response->assertSee('hello');


    }

    public function test_user_is_guest()
    {
        AuthFacade::shouldReceive('check')->once()->andReturn(true);
        UserProviderFacade::shouldReceive('getUserByEmail')->never();
        UserProviderFacade::shouldReceive('isBanned')->never();
        TokenGeneratorFacade::shouldReceive('generateToken')->never();
        TokenStoreFacade::shouldReceive('saveToken')->never();
        TokenSenderFacade::shouldReceive('send')->never();
        ResponderFacade::shouldReceive('youShouldBeGuest')->once()->andReturn(response('hello'));
        $response = $this->json('GET', 'api/tow-factor-auth/request-token?email=baran@yahoo.com', ['Accept' => 'application/json']);
        $response->assertSee('hello');


    }
    public function test_the_happy_path_for_login()
    {
       // $this->withoutExceptionHandling();

        TokenStoreFacade::shouldReceive('getUidByToken')
            ->once()
            ->with('123456')
            ->andReturn(1);
        AuthFacade::shouldReceive('loginById')
            ->once()
            ->with(1);

        ResponderFacade::shouldReceive('loggedIn')->once();
        $this->json('GET', 'api/tow-factor-auth/login?token=123456', ['Accept' => 'application/json']);


    }

    public function test_Token_is_not_valid()
    {

        TokenStoreFacade::shouldReceive('getUidByToken')
            ->once()
            ->with('123456')
            ->andReturn(null);
        AuthFacade::shouldReceive('loginById')->never();
        ResponderFacade::shouldReceive('tokenNotFound')->once();
        $this->json('GET', 'api/tow-factor-auth/login?token=123456', ['Accept' => 'application/json']);


    }

}
