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
    public const PROFILE_UPDATE = 'profile.update';
    public const PROFILE_PREFERENCES_UPDATE = 'profile.preferences.update';
    public const PROFILE_AVATAR_STORE = 'profile.avatar.store';
    public const PROFILE_AVATAR_DESTROY = 'profile.avatar.destroy';

    //---Organization----------------

    public const ORGANIZATION_STORE = 'organizations.store';
    public const ORGANIZATION_INDEX = 'organizations.index';
    public const ORGANIZATION_SHOW = 'organizations.show';
    public const ORGANIZATION_UPDATE = 'organizations.update';
    public const ORGANIZATION_DESTROY = 'organizations.destroy';
    public const ORGANIZATION_MEMBERS_INDEX = 'organizations.members.index';
    public const ORGANIZATION_MEMBERS_UPDATE = 'organizations.members.update';
    public const ORGANIZATION_MEMBERS_DESTROY = 'organizations.members.destroy';
    public const ORGANIZATION_LEAVE = 'organizations.leave';
    public const ORGANIZATION_TRANSFER_OWNERSHIP = 'organizations.transfer-ownership';

    //---Project----------------

    public const PROJECT_STORE = 'projects.store';
    public const PROJECT_INDEX = 'projects.index';
    public const PROJECT_SHOW = 'projects.show';
    public const PROJECT_UPDATE = 'projects.update';
    public const PROJECT_DESTROY = 'projects.destroy';
    public const PROJECT_ARCHIVE = 'projects.archive';

    //---Tasks----------------
    public const TASK_STORE = 'tasks.store';
    public const TASK_INDEX = 'tasks.index';
    public const TASK_SHOW = 'tasks.show';
    public const TASK_UPDATE = 'tasks.update';
    public const TASK_DESTROY = 'tasks.destroy';
    public const TASK_STATUS_UPDATE = 'tasks.status.update';
    public const TASK_ASSIGNEE_UPDATE = 'tasks.assignee.update';
    public const TASK_PRIORITY_UPDATE = 'tasks.priority.update';
    public const TASK_SUBTASKS_STORE = 'tasks.subtasks.store';
    public const TASK_SUBTASKS_INDEX = 'tasks.subtasks.index';
    public const TASK_ACTIVITY_INDEX = 'tasks.activity.index';
    public const TASK_STATUS_BULK = 'tasks.status.bulk';
    public const TASK_COLUMN_MOVE = 'tasks.column.move';

    //---Boards----------------------
    public const BOARD_INDEX = 'boards.index';
    public const BOARD_STORE = 'boards.store';
    public const BOARD_SHOW = 'boards.show';
    public const BOARD_UPDATE = 'boards.update';
    public const BOARD_DESTROY = 'boards.destroy';

    //---Columns-----------------------
    public const COLUMN_STORE = 'columns.store';
    public const COLUMN_UPDATE = 'columns.update';
    public const COLUMN_REORDER = 'columns.reorder';
    public const COLUMN_DESTROY = 'columns.destroy';
}
