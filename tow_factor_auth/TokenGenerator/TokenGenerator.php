<?php


namespace TowFactorAuth\TokenGenerator;


class TokenGenerator
{
    function generateToken()
    {
        return  rand(1000000, 10000000 - 1);

    }
}
