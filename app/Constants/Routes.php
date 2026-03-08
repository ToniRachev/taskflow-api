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

    public const GET_PROFILE = 'profile.show';
    public const UPDATE_PROFILE = 'profile.update';
    public const UPDATE_PREFERENCES = 'profile.preferences.update';
    public const STORE_AVATAR = 'profile.avatar.store';
    public const DESTROY_AVATAR = 'profile.avatar.destroy';

    //---Organization----------------

    public const STORE_ORGANIZATION = 'organizations.store';
    public const GET_USER_ORGANIZATIONS = 'organizations.index';
    public const GET_ORGANIZATION_DETAILS = 'organizations.show';
    public const UPDATE_ORGANIZATION = 'organizations.update';
    public const DESTROY_ORGANIZATION = 'organizations.destroy';
    public const GET_ORGANIZATION_MEMBERS = 'organizations.members.index';
    public const UPDATE_ORGANIZATION_MEMBER_ROLE = 'organizations.members.update';
    public const DESTROY_ORGANIZATION_MEMBER = 'organizations.members.destroy';
    public const LEAVE_ORGANIZATION = 'organizations.leave';
    public const TRANSFER_ORGANIZATION_OWNERSHIP = 'organizations.transfer-ownership';

    //---Project----------------

    public const STORE_PROJECT = 'projects.store';
    public const INDEX_PROJECT = 'projects.index';
    public const SHOW_PROJECT = 'projects.show';
    public const UPDATE_PROJECT = 'projects.update';
    public const DESTROY_PROJECT = 'projects.destroy';
    public const ARCHIVE_PROJECT = 'projects.archive';
}
