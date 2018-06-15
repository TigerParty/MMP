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
    'map_key' => config('argodf.googel_map_api_key'),

    'sms' => [
        'title' => 'sms',
        'offical_phone_code' => '9696',
        'description' => [
            'sub_title' => 'Contact '.trans('site.shorthead_title').' with any infrastructure issues',
            'content' => 'You will receive a confirmation SMS that your message has been received. Once your message has been moderated it will appear on this page. We take note of all your feedback and will post our responses publically here. However, we will not send individual replies unless in exceptional circumstances.',
            'show_btn' => 'Read more',
            'hide_btn' => 'Show less'
        ],
        'counter' => [
            'new' => 'new',
            'total' => 'total',
            'unresponded' => 'unresponded',
            'responded' => 'responded'
        ],
        'data_list' => [
            'order' => 'recent'
        ],
        'message_log' => [
            'from' => 'from',
            'send_btn' => 'send',
            'message_input_placeholder' => 'Type your message here…',
            'delete_btn' => 'delete'
        ]
    ],
    'report' => [
        'title' => 'citizen',
        'offical_phone_code' => 'APP',
        'description' => [
            'sub_title' => 'Contact '.trans('site.shorthead_title').' with any infrastructure issues'
        ],
        'counter' => [
            'new' => 'new',
            'total' => 'total'
        ],
        'data_list' => [
            'order' => 'recent'
        ],
        'message_log' => [
            'from' => 'from',
            'delete_btn' => 'delete'
        ]
    ],
    'voice' => [
        'title' => 'DIAL',
        'offical_phone_code' => '9696',
        'description' => [
            'sub_title' => 'Contact '.trans('site.shorthead_title').' with any infrastructure issues',
            'content' => 'You will receive a confirmation SMS that your message has been received. Once your message has been moderated it will appear on this page. We take note of all your feedback and will post our responses publically here. However, we will not send individual replies unless in exceptional circumstances.',
            'show_btn' => 'Read more',
            'hide_btn' => 'Show less'
        ],
        'counter' => [
            'new' => 'new',
            'total' => 'total',
            'unresponded' => 'unresponded',
            'responded' => 'responded'
        ],
        'data_list' => [
            'order' => 'recent'
        ],
        'message_log' => [
            'from' => 'from'
        ]
    ],
    'comment' => [
        'title' => 'COMMENT',
        'offical_phone_code' => '',
        'description' => [
            'sub_title' => 'Contact '.trans('site.shorthead_title').' with any infrastructure issues',
            'content' => 'Feel free to leave your comments on the platform regarding any infrastructure issues.',
            'show_btn' => 'Read more',
            'hide_btn' => 'Show less'
        ],
        'counter' => [
            'new' => 'new',
            'total' => 'total',
            'unresponded' => 'unresponded',
            'responded' => 'responded'
        ],
        'data_list' => [
            'order' => 'recent'
        ],
        'message_board' => [
            'order' => 'newest',
            'unit' => 'comment',
            'host_name' => 'Argo',
            'reply_btn' => 'Reply',
            'delete_btn' => 'Delete',
            'post_btn' => 'POST',
            'placeholders' => [
                'name' => 'Your Name',
                'email' => 'Your email',
                'typing_bar' => 'Typing your comment...',
                'reply_box' => 'Type reply here',
            ]
        ]
    ],

];
