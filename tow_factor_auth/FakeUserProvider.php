<?php


namespace TowFactorAuth;


use App\Models\User;

class FakeUserProvider
{
     function getUserByEmail($email)
    {
        User::unguard();
       return new User(['id' => 1, 'email' => 'atefe.boluri@yahoo.com']);
    }
    function isBanned($user){

         return false;
    }

}
