<?php


namespace TowFactorAuth\TokenStore;


class FakeTokenStore
{

    function saveToken($token, $userId)
    {

    }
    function getUidByToken($token){

        return 1;
    }
}
