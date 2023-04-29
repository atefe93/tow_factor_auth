<?php


namespace TowFactorAuth\Http;


use Imanghafoori\SmartFacades\Facade;
use TowFactorAuth\Http\Responses\AndroidResponses;
use TowFactorAuth\Http\Responses\VueResponses;

class ResponderFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        if (request('client') == 'android') {
            return AndroidResponses::class;
        }
        return VueResponses::class;
    }


}
