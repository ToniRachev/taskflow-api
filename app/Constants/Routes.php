<?php

namespace App\Constants;

class Routes
{
    public const API_VERSION = 'api.v1';
    public const AUTH_MODULE = 'auth';

    //---Auth----------------

    public const LOGIN = 'login';
    public const REGISTER = 'register';
    public const LOGOUT = 'logout';
    public const LOGOUT_ALL = 'logout-all';
    public const REFRESH_TOKEN = 'refresh-token';

    //---Profile----------------

    public const GET_PROFILE = 'me';
    public const PROFILE = 'profile';
    public const PREFERENCES = 'preferences';
    public const AVATAR = 'avatar';
}
