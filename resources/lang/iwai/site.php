<?php

return array(

    /*
    |--------------------------------------------------------------------------
    | Site Global
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default naming of ArgoDF Site
    |
    */
    'shorthead_title' => 'IWAI',
    'shorthead_subtitle' => 'Jal Marg Vikas Project',
    'title'   => 'IWAI',
    'subtitle'   => 'Jal Marg Vikas Project',
    'subject' => 'Smarter monitoring for a smarter IWAI!',
    'email_name' => 'IWAI',

    /*
    |--------------------------------------------------------------------------
    | Naming of Nav bar
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default naming of ArgoDF form buttons
    |
    */
    'nav_bar' => array(
        'home'    => 'Home',
        'explore_dw' => 'Search',
        'map'     => 'Explore',
        'project'   => 'Project',
        'data_dw' => 'Data Tool',
        'chart' => 'Featured Data',
        'chart_gs' => 'Chart GS',
        'citizen_sms' => 'Citizen FeedBack',
        'voto_voice' => 'Voice',
        'download_app'   => 'Download App',
        'data_analysis' => 'Data Analysis',
        'syllabus' => 'Syllabus',
        'how_it_works' => array(
            'main_title' => 'How It Works',
            'app' => 'APP'
        ),
        'admin_panel' => array(
            'name' => 'ADMIN PANEL',
            'form' => 'MANAGE FORMS',
            'category' => 'MANAGE CATEGORIES',
            'notification' => "MANAGE NOTIFICATIONS",
            'download_data' => 'DOWNLOAD DATA',
            'status' => 'MANAGE STATUS LABELS',
            'customize_homepage' => 'CUSTOMIZE HOMEPAGE',
            'user_management' => 'USER MANAGEMENT',
        )
    ),

    'navigator' => array(
        'content' => array(
            'line1' => 'Track Progress Convey Your Feedback',
            'line2' => 'Interact with authorities',
            'line3' => ''),
        'sms_intro' => array(
            'text' => 'To send a SMS to IWAI, text +12345',
            'btn' => 'Views SMS Messages'),
        'quick_links' => array(
            'search' => array('text' => 'search'),
            'submit' => array('text' => 'submit'),

        )
    ),

    'recent_progress' => array(
        'title' => array(
            'line1' => 'recent',
            'line2' => 'progress'
        ),
        'quick_links' => 'View All :name',
        'project_unit' => 'Project',
        'article' => array(
            'last_updated' => 'Last updated:',
            'status' => 'Status'
        )
    ),

    'how_it_work' => array(
        'download_app' => array(
            'title' => array(
                'line1' => 'How it works/',
                'line2' => 'App'
            ),
            'quick_links' => 'Download App',
            'tutorial' => array(
                'step_2' => array(
                    'img' => 'images/app_tutorial/2step.png',
                    'description' => 'If you are submitting a new report for a new or existing complaint, tap the top button "Submit a Report"'),
                'step_3' =>array(
                    'img' => 'images/app_tutorial/3step.png',
                    'description' => 'Take a photo now or choose one from your device'),
                'step_4' => array(
                    'img' => 'images/app_tutorial/4step.png',
                    'description' => 'Complaints near you appear on this map as pins. You can select one by using the dropdown menu under the map'),
                'step_5' => array(
                    'img' => 'images/app_tutorial/5step.png',
                    'description' => 'You can add more details about the complaint here'),
                'step_6' => array(
                    'img' => 'images/app_tutorial/6step.png',
                    'description' => 'To review submitted report(s), please visit ' . url(''))
            )
        )
    ),

    'map' => array(
        'show_projects' => 'Show projects',
        'show_trackers' => 'Show Trackers',
        'show_iri_trackers' => 'Show IRI Trackers',
        'show_reporter_location' => 'Show Reporter Location',
        'tutorial' => array(
            'Show Issues' => array('img' => '/images/map_tutorial_project.png',
                                     'description' => 'When you check "Show Projects", all Project will appear with blue dot icons.'),
            'Show Trackers' => array('img' => '/images/map_tutorial_tracker.png',
                                     'description' => 'When you check "Show Trackers", the current speed of a path will appear with lines. Once you click the line, you will see more information of the path.Color of the line indicate speed od the road. You can see the reference of the color on the top right.'),
            'Choose Category' => array('img' => '/images/map_tutorial_cgy.png',
                                       'description' => 'You can choose any category on the left. The project under the category you choose will appear with a blue dot on the map.')
        )
    ),

    /*
    |--------------------------------------------------------------------------
    | Warning messages
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the warning messages for argodf
    |
    */
    'warning' => array(
        'not_enouth_permission_to_access_page' => "You don't have enough permission to access this page."
    ),

);
