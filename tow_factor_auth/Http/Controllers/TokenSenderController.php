<?php


namespace TowFactorAuth\Http\Controllers;


use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use TowFactorAuth\Facades\AuthFacade;
use TowFactorAuth\Facades\TokenGeneratorFacade;
use TowFactorAuth\Facades\TokenSenderFacade;
use TowFactorAuth\Facades\TokenStoreFacade;
use TowFactorAuth\Facades\UserProviderFacade;
use TowFactorAuth\Http\ResponderFacade;


class TokenSenderController extends Controller
{
    public function issueToken()
    {
        $email = request('email');

        $this->validateEmailIsValid();
        $this->checkUserIsGuest();

        //1.find user row in db or fail
        $user = UserProviderFacade::getUserByEmail($email);
        if (!$user){
            return ResponderFacade::userNotFound();
        }
        //2.stop block users
        if (UserProviderFacade::isBanned($user)) {
            return ResponderFacade::blockedUser();
        }
        //3.generate token
        $token = TokenGeneratorFacade::generateToken();
        //4.save token
        TokenStoreFacade::saveToken($token, $user->id);
        //5.send token
        TokenSenderFacade::send($token, $user);
        //6.send response
        return ResponderFacade::tokenSent();

    }

    public function loginWithToken()
    {

        $token=request('token');

        $uid=TokenStoreFacade::getUidByToken($token);
        if (!$uid){
            return ResponderFacade::tokenNotFound();
        }
        AuthFacade::loginById($uid);
        return ResponderFacade::loggedIn();

    }

    private function validateEmailIsValid()
    {
        $v = Validator::make(request()->all(), ['email' => 'email|required']);
        if ($v->fails()) {
            ResponderFacade::emailNotValid()->throwResponse();
        }
    }

    private function checkUserIsGuest()
    {
        if (AuthFacade::check()) {
            ResponderFacade::youShouldBeGuest()->throwResponse();
        }
    }


}
