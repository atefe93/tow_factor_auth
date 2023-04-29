<?php


namespace TowFactorAuth\TokenStore;


class TokenStore
{

    function saveToken($token, $userId)
    {
        $ttl=config('tow_factor_config.token_ttl');
        cache()->set($token . '_2factor_auth', $userId, $ttl);
    }
    function getUidByToken($token){

        return cache()->pull($token . '_2factor_auth');
    }
}
