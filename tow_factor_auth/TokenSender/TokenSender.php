<?php


namespace TowFactorAuth\TokenSender;



use Illuminate\Support\Facades\Notification;


class TokenSender
{
    function send($token, $user)
    {

        Notification::sendNow($user,new LoginTokenNotification($token));

    }
}
