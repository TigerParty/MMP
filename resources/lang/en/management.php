<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Pagination Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used by the paginator library to build
    | the simple pagination links. You are free to change them to anything
    | you want to customize your views to better match your application.
    |
    */
    'user' => [
        'title' => 'user management',
        'function' => [
            'add_user' => 'add a user',
            'show_user_role' => 'see user roles'
        ],
        'fields' => [
            'project' => 'issue'
        ],
        'modals' => [
            'add_user' => [
                'title' => 'add user'
            ],
            'edit_user' => [
                'title' => 'edit user'
            ],
            'add_project' => [
                'title' => 'issue'
            ],
            'user_role' => [
                'title' => 'user role'
            ],
        ]
    ]
];
