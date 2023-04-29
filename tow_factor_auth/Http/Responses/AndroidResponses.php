<?php


namespace TowFactorAuth\Http\Responses;


use Illuminate\Http\Response;

class AndroidResponses
{
    public function youShouldBeGuest()
    {
        return response()->json(['error' => 'Your are logged in'],Response::HTTP_BAD_REQUEST);
    }
    public function emailNotValid()
    {
        return response()->json(['error' => 'Your email is not valid'],Response::HTTP_BAD_REQUEST);
    }
    public function blockedUser()
    {
        return response()->json(['err' => 'the account is blocked'],Response::HTTP_BAD_REQUEST);
    }
    public function tokenSent()
    {
        return response()->json(['msg' => 'token was sent to your app.'],Response::HTTP_OK);
    }
    public function loggedIn()
    {
        return response()->json(['msg' => 'You are logged in.'],Response::HTTP_OK);
    }
    public function tokenNotFound()
    {
        return response()->json(['error' => 'Token is not valid'],Response::HTTP_BAD_REQUEST);

    }
    public function userNotFound()
    {
        return response()->json(['error' => 'Email Dose Not Exist'],Response::HTTP_BAD_REQUEST);
    }
}
