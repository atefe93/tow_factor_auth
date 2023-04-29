<?php


namespace TowFactorAuth;


use App\Models\User;

class UserProvider
{
     function getUserByEmail($email)
    {
        return User::where('email', $email)->first();

    }
    function isBanned($user){

         return $user->is_ban == 1 ? true : false;
    }

}
