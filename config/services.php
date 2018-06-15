<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, SparkPost and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
    ],

    'ses' => [
        'key' => env('SES_KEY'),
        'secret' => env('SES_SECRET'),
        'region' => 'us-east-1',
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

    'stripe' => [
        'model' => App\Argo\User::class,
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
    ],

    //-- Facebook comment plugin
    'fb_comment_plugin' => [
        'app_id' => env('SITE_FB_COMMENT_PLUGIN_APP_ID', null)
    ],

    //-- Virustotal
    'virustotal' => [
        'apikey' => env('SITE_VIRUSTOTAL_APIKEY', null)
    ],

    //-- Google analysis
    'google_analysis' => [
        'id' => env('SITE_GOOGLE_ANALYSIS_ID', null)
    ],

    //-- Survey Services
    'survey' => [
        'default' => env('SURVEY_DRIVER', 'mailbox'),

        'drivers' => [
            'mailbox' => [
                'host' => env('SURVEY_MAILBOX_HOST'),
                'username' => env('SURVEY_MAILBOX_USERNAME'),
                'password' => env('SURVEY_MAILBOX_PASSWORD'),
                'protocol' => env('SURVEY_MAILBOX_PROTOCOL', 'imap'),
                'encryption' => env('SURVEY_MAILBOX_ENCRYPTION', 'ssl'),
                'fetch_rule' => env('SURVEY_MAILBOX_FETCH_RULE', 'SUBJECT "ArgoPBX Voicemail Notification" UNSEEN'),
            ],

            'voto' => [
                'survey_id' => env('SURVEY_VOTO_SID', null),
                'api_key' => env('SURVEY_VOTO_API_KEY', null),
            ],
        ]
    ]
];
