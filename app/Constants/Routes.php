<?php

namespace App\Constants;

class Routes
{

    //---Auth----------------
    public const LOGIN = 'auth.login';
    public const REGISTER = 'auth.register';
    public const LOGOUT = 'auth.logout';
    public const LOGOUT_ALL = 'auth.logout-all';
    public const REFRESH_TOKEN = 'auth.refresh-token';

    //---Profile----------------

    public const GET_PROFILE = 'profile';
    public const UPDATE_PROFILE = 'profile';
    public const PREFERENCES = 'profile.preferences';
    public const STORE_AVATAR = 'profile.avatar';
    public const DESTROY_AVATAR = 'profile.avatar';
}
