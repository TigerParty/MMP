<?php

return array(
    'reload_hash' => env('RELOAD_HASH', ''),

    //-- Enable/disable Customize homepage
    'customize_homepage_enabled' => env('SITE_CUSTOMIZE_HOMEPAGE_ENABLED', false),

    //-- Enable/disable citizen SMS
    'citizen_sms_enabled' => env('SITE_CITIZEN_SMS_ENABLED', true),

    //-- Enable/disable Voto Voice
    'voto_voice_enabled' => env('SITE_VOTO_VOICE_ENABLED', false),

    //-- Enable/disable Syllabus
    'syllabus_enabled' => env('SITE_SYLLABUS_ENABLED', false),

    //-- Enable/disable Navbar External links
    'external_nav_links' => json_decode(env('SITE_EXTERNAL_NAV_LINKS', '[]'), true),

    //-- Enable/disable Menu External links
    'external_menu_links' => json_decode(env('SITE_EXTERNAL_MENU_LINKS', '[]'), true),

    //-- Enable/disable Project Status Label
    'project_status_enabled' => env('SITE_PROJECT_STATUS_ENABLED', true),

    //-- Auto merge received report from App
    'auto_approved_submitted_report' => env('SITE_AUTO_APPROVED_SUBMITTED_REPORT', true),

    //-- Enable/Disable user management
    'user_management_enabled' => env('SITE_USER_MANAGEMENT_ENABLED', true),

    //-- Enable/Disable new Explore pgae
    'new_map_page_enabled' => env('SITE_NEW_MAP_PAGE_ENABLED', true),

    //-- Default container id for App creating Projects
    'default_container_id' => env('SITE_DEFAULT_CONTAINER_ID', 1),

    //-- Default region ids for App createing Projects
    'default_region_ids' => array_filter(explode(',', env('SITE_DEFAULT_REGION_IDS', ''))),

    'homepage_chart' => env('SITE_HOMEPAGE_CHART', 'project_count_group_region'),

    //-- Enable/Disable explore page map options.
    'map_options' => array(
        'project' => env('SITE_MAP_LAYER_OPTIONS_PROJECT', true),
        'citizen_report' => env('SITE_MAP_LAYER_OPTIONS_CITIZEN_REPORT', true),
        'reporter_location' => env('SITE_MAP_LAYER_OPTIONS_REPORTER_LOCATION', true),
        'tracker' => env('SITE_MAP_LAYER_OPTIONS_TRACKER', true),
    ),

    'rsa_key' => array(
        'private' => env('SITE_RSA_PRIVATE_KEY', '/rsa.key'),
        'public' => env('SITE_RSA_PUBLIC_KEY', '/rsa_pub.key'),
    ),

    //rootRegion's svg ViewBox Size
    'region_svg_viewBox' => env('SITE_REGION_SVG_VIEWBOX', "0 0 800 816"),

    //-- Notification Settings
    'notification' => array(
        'fb_comment' => array(
            'enabled' => env('SITE_FB_COMMENT_NOTIFICATION_ENABLED', true),
            'receivers' => config('notification_receivers.'.env('SITE_NOTIFICATION_RECEIVER_LIST', 'developer').'.fb_comment', array())
        ),
        'new_report_created' => array(
            'enabled' => env('SITE_NEW_REPORT_CREATED_NOTIFICATION_ENABLED', true),
            'receivers' => config('notification_receivers.'.env('SITE_NOTIFICATION_RECEIVER_LIST', 'developer').'.new_report_created', array())
        ),
        'scheduled_report' => array(
            'enabled' => env('SITE_SCHEDULED_REPORT_NOTIFICATION_ENABLED', true),
            'receivers' => config('notification_receivers.'.env('SITE_NOTIFICATION_RECEIVER_LIST', 'developer').'.scheduled_report', array())
        )
    ),

    //-- Site Setting
    'require_login' => env('SITE_REQUIRE_LOGIN', false),
    'ng_date_format' => env('SITE_NG_DATE_FORMAT', 'd MMM, yyyy'),
    'ng_datetime_format' => env('SITE_NG_DATETIME_FORMAT', 'hh:mm a, d MMM yyyy'),
    'php_date_format' => env('SITE_PHP_DATE_FORMAT', 'd M, Y'),
    'php_datetime_format' => env('SITE_PHP_DATETIME_FORMAT', 'h:i a d M, Y'),
    'download_datetime_format' => env('SITE_DOWNLOAD_DATETIME_FORMAT', 'Y-m-d_H-i-s'),

    //-- Group ID, if this site is master, value = all group ids.
    'group_id' => explode(',', env('SITE_GROUP_ID', '1')),

    //-- Blade View injector, set to false to disable meta in global
    'head_meta' => env('SITE_HEAD_META', 'master.head_meta'),

    //-- Theme
    'theme' => array(
        'css' => env('SITE_THEME_CSS', 'legacy/css/theme-lib.css'),
        'logo' => env('SITE_THEME_LOGO', 'legacy/images/Coat_of_arms_of_Liberia.png'),
        'navigator' => env('SITE_THEME_NAVIGATOR', 'legacy/images/ArgoLiberia_home_bkg.jpg'),
    ),

    //-- Fallback image path (relative to public folder)
    'fallback_image' => env('SITE_FALLBACK_IMAGE_PATH', 'legacy/images/default_thumb.png'),

    //-- Project default image
    'default_project_logo' => env('SITE_DEFAULT_PROJECT_LOGO_PATH', 'legacy/images/default-project-image-liberia.jpg'),

    //-- Default Permission Levels
    'default_perm' => array(
        'project' => array(
            'view' => env('SITE_DEFAULT_PERMISSION_PROJECT_VIEW', 5), // Public
            'edit' => env('SITE_DEFAULT_PERMISSION_PROJECT_EDIT', 3), // Coordinator
        ),
        'project_for_app' => array(
            'view'  => env('SITE_DEFAULT_PERMISSION_PROJECT_FOR_APP_VIEW', 5), // Public
            'edit'  => env('SITE_DEFAULT_PERMISSION_PROJECT_FOR_APP_EDIT', 5), // Public
        ),
        'report' => array(
            'view'  => env('SITE_DEFAULT_PERMISSION_REPORT_VIEW', 5), // Public
            'edit'  => env('SITE_DEFAULT_PERMISSION_REPORT_EDIT', 3), // Coordinator
        ),
        'field' => array(
            'view'   => env('SITE_DEFAULT_PERMISSION_FIELD_VIEW', 5), // Public
            'edit'   => env('SITE_DEFAULT_PERMISSION_FIELD_EDIT', 3), // Coordinator
        ),
    ),
    'default_view_priority' => env('USER_DEFAULT_VIEW_PRIORITY', 5),
    'delete_priority' => env('SITE_DEFAULT_DELETE_PRIORITY', 2),
    'admin_function_priority' => env('SITE_DEFAULT_ADMIN_FUNCTION_PRIORITY', 3),
    'sms_number_visible_priority' => env('SITE_SMS_NUMBER_VISIBLE_PRIORITY', 2),

    'home_map_center' => array(
        'lat' => (float)env('SITE_EXPLORE_MAP_CENTER_LAT', 6.290961),
        'lng' => (float)env('SITE_EXPLORE_MAP_CENTER_LNG', -10.578492),
        'zoom' => (float)env('SITE_EXPLORE_MAP_CENTER_ZOOM', 8)
    ),
    'inner_map_center' => array(
        'lat' => (float)env('SITE_INNER_MAP_CENTER_LAT', 6.290961),
        'lng' => (float)env('SITE_INNER_MAP_CENTER_LNG', -9.578492),
        'zoom' => (float)env('SITE_INNER_MAP_CENTER_ZOOM', 7)
    ),

    'app_version' => env('APP_VERSION', '4_0_0'),
    'app_eliminate_update_notify' => env('APP_ELIMINATE_UPDATE_NOTIFY', false),
    'app_android_download_link' => env('APP_ANDROID_DOWNLOAD_LINK', "/apk/argo.apk"),
    'google_market_link' => env('GOOGLE_MARKET_LINK', false),
    'googel_map_api_key' => env('GOOGLE_MAP_API_KEY', 'AIzaSyCrHMOa5AB4AEEqLlx3WtkROAmHzzi20vI'),
    'feedback_phone_code' => env('FEEDBACK_PHONE_CODE', '9966'),
);
