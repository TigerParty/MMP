<?php

return array(
    /*
    |--------------------------------------------------------------------------
    | Naming of Form
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default naming of ArgoDF form fields
    |
    */
    'static_fields'   => 'STATIC FIELDS',
    'created_by'      => 'Created By',
    'public_setting'  => 'Public Setting',
    'latitude'        => 'Latitude',
    'longitude'       => 'Longitude',
    'form_type'       => 'Form Type',
    'description'     => 'Description',

    /*
    |--------------------------------------------------------------------------
    | Naming of Buttons
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default naming of ArgoDF form buttons
    |
    */
    'btn' => array(
        'login'     => 'Login',
        'logout'    => 'Logout',
        'signin'    => 'Sign in',
        'view'      => 'VIEW',
        'edit'      => 'EDIT',
        'cancel'    => 'Cancel',
        'submit'    => 'Submit',
        'detail'    => 'Detail',
        'downloadcsv'    => 'Download CSV',
        'createchart'    => 'Create Chart',
        'link'      => 'Link',
        'delete'    => 'Delete',
        'yes'       => 'Yes',
        'no'        => 'No'
    ),

    /*
    |--------------------------------------------------------------------------
    | Naming of Login
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default naming of ArgoDF Login
    |
    */

    'login' => array(
        'name'     => 'Please Login to Your Account',
        'account'  => 'User Name',
        'password' => 'Password'
    ),

    /*
    |--------------------------------------------------------------------------
    | Naming of Project
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default naming of ArgoDF Project
    |
    */
    'project' => array(
        'name' => 'Issues',
        'btn' => array(
            'create' => 'Create New Issue',
            'edit'   => 'Edit Issue Profile'
        ),
        'component' => array(
            'info'        => 'BASIC INFO',
            'latest_data' => 'Select the kind of data you want to view',
            'form_type'   => 'Form Type',
            'location'    => 'LOCATION',
            'image'       => 'IMAGES',
            'categories'  => 'CATEGORIES',
            'comments'    => 'COMMENTS',
            'report'      => 'Report',
            'logo'        => 'Logo',
            'no_report'   => 'No report to display',
            'report_link' => 'Click on a date to learn more about that report',
        ),
        'field' => array(
            'title'             => 'Title',
            'select'            => 'Select',
            'region'            => array(
                'name'   => 'County',
                'select' => '- Select County -' ),
            'district'            => array(
                'name'   => 'District',
                'select' => '- Select District -' ),
            'initial_date'      => 'Date Issue Record was Created',
            'created_by'        => 'Created by',
            'created_on'        => 'Created on',
            'last_updated'      => 'Last Updated',
            'description'       => 'Description',
            'default_image'     => 'Default Image',
            'cover_image'     => 'Cover Image',
            'default_form_type' => array(
                'name'   => 'Default Report Form Type',
                'select' => ' - Select Dynamic Form - '
            ),
            'status' => array(
                'name'   => 'Status',
                'select' => ' - Select Project Status - '
            ),
            'gps' => array(
                'avg_speed' => 'Average speed',
                'distance' => 'Distance',
                'start_time' => 'Start time',
                'end_time' => 'End time',
            )
        ),
        'index' => array(
            'search'       => 'Search for issue or county',
            'last_updated' => 'Last Updated',
        ),
        'create' => array(
            'name' => 'Create Issue'
        ),
        'show' => array(
            'name'        => 'Show Issue',
        ),
        'edit' => array(
            'name' => 'Edit Issue'
        )
    ),

    /*
    |--------------------------------------------------------------------------
    | Naming of Report
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default naming of ArgoDF Report
    |
    */
    'report' => array(
        'name'  => 'Below is a listing of all reports in chronological order belonging to this Issue',
        'btn' => array(
            'create' => 'Create Report',
            'back' => 'Back To Issue'
        ),
        'component' => array(
            'info'        => 'BASIC INFO',
            'dynamic_fields' => 'DYNAMIC FIELDS',
            'location'    => 'LOCATION',
            'comments' => 'COMMENTS'
        ),
        'field' => array(
            'report_to' => 'Report To',
            'version'   => 'Version',
            'title' => 'Title',
            'description' => 'Description',
            'lat' => 'Latitude',
            'lng' => 'Longitude',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'initial_date' => 'Reported Date',
            'location' => 'Location',
            'dynamic_form' => array(
                'name' => 'Dynamic Form',
                'select' => ' - Select Dynamic Form - '
            ),
            'assigned_to' => 'Assigned To'
        ),
        'title' => array(
            'create' => 'Create Report',
            'show' => 'Show Report'
        )
    ),

    /*
    |--------------------------------------------------------------------------
    | Naming of Dynamic Form
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default naming of ArgoDF Subject
    |
    */
    'dynamic_form' => array(
        'name'  => 'Forms',
        'btn' => array(
            'create' => 'Create New Form',
            'edit' => 'Edit Form Profile',
            'delete' => 'Delete this Field',
            'add_field' => 'Add Field',
            'back' => 'Back to List'
        ),
        'component' => array(
            'info' => 'BASIC INFO',
            'fields' => 'FIELDS'
        ),
        'field' => array(
            'name' => 'Name',
            'type' => array(
                'name' => 'Report Type',
                'select' => ' - Select Report Type - '
            )
        ),
        'dync_field' => array(
            'name' => 'Name',
            'template_type' => array(
                'name' => 'Template Type',
                'select' => ' - Select Template Type - '
            ),
            'default' => 'Default Value',
            'options' => 'Options',
            'show_if' => array(
                'checkbox' => 'Only show this question based on the answer to one of the previous questions',
                'if_the_answer_to' => 'If the answer to',
                'is' => 'is',
                'select_question' => ' - Select question - ',
                'select_answer' => ' - Select answer - '
            ),
        ),
        'title' => array(
            'create' => 'Create Form',
            'show' => 'Show Form',
            'edit' => 'Edit Form'
        )
    ),

    /*
    |--------------------------------------------------------------------------
    | Naming of Attachment
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default naming of ArgoDF Attachment
    |
    */
    'attachment' => array(
        'upload' => 'Attachment Upload',
        'file'   => 'Attachments',
        'image'  => 'Gallery'
    ),

    /*
    |--------------------------------------------------------------------------
    | Naming of Category
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default naming of ArgoDF Categories
    |
    */
    'category' => array(
        'name' => 'Categories',
        'btn' => array(
            'add' => 'Add Category',
            'clear' => 'Clear Edits',
            'save' => 'Save Changes'
        ),
        'component'   => array(
            'new' => 'New Category',
            'list' => 'Current Categories'
        ),
        'field' => array(
            'name' => 'Category Name',
            'nest' => 'Nest Category Under',
            'select' => ' - Select Category - '
        ),
        'placeholder' => array(
            'new_cgy_name' => "New Category Name"
        ),
        'filter' => 'CATEGORY FILTER',
        'manage' => 'Manage Category'
    ),

    /*
    |--------------------------------------------------------------------------
    | Naming of Category
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default naming of ArgoDF Categories
    |
    */
    'status' => array(
        'name' => 'Status',
        'filter' => 'STATUS FILTER'
    ),

    /*
    |--------------------------------------------------------------------------
    | Naming of Admin
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default naming of ArgoDF Admin
    |
    */

    'admin' => array(
        'name'        => 'ADMIN FUNCTIONS',
        'approved'  => 'approved',
        'auto_merge' => 'Approve',
        'view_level'  => array(
            'name'   => 'View Level',
            'select' => ' - Select View Level - '
        ),
        'edit_level'  => array(
            'name'   => 'Edit Level',
            'select' => ' - Select Edit Level - '
        ),
        'btn' => array(
            'back' => 'Back To Admin Panel'
        )
    ),

    /*
    |--------------------------------------------------------------------------
    | Naming of Download
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default naming of ArgoDF Download
    |
    */

    'download_data' => array(
        'name'        => 'Download Data',
        'subtitle'  => array(
            'csv' => 'Download CSV',
            'chart' => 'Data Analysis'
        ),
        'component'   => array(
            'category' => 'Category Filter',
            'field'     => 'Fields',
            'xfield'    => 'X-Axis',
            'yfield'    => 'Y-Axis',
            'dynamic_form' => array(
                'title' => 'Form Type',
                'select' => ' - Select - '
            ),
            'chart' => '-Select chart type'
        ),
        'static_fields' => array(
            'project_title' => 'Issue Name',
            'report_title' => 'Report Title',
            'report_description' => 'Report Description',
            'report_lng' => 'Report Longitude',
            'report_lat' => 'Report Latitude',
            'report_createby' => 'Report Created By'
        ),
        'field_for_x' => array(
            'project_title' => 'Issue Name',
            'report_title' => 'Report Title',
            'report_description' => 'Report Description',
            'report_lng' => 'Report Longitude',
            'report_lat' => 'Report Latitude',
            'report_createby' => 'Report Created By'
        ),
        'field_for_y' => array(
            'report_count' => 'Report Count'
        )
    ),

    'notification' => array(
        'name'      => 'Manage Reports',
        'component' => array(
            'email' => 'E-mail Notification'
        ),
        'link' => array(
            'add' => 'Add'
        )
    ),

    /*
    |--------------------------------------------------------------------------
    | Naming of Data Analysis
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default naming of ArgoDF Data Analysis
    |
    */

    'data_analysis' => array(
        'name' => 'Data Analysis',
        'subtitle'  => array(
            'csv' => 'Download CSV',
            'chart' => 'Data Visualization'
        ),
        'component'   => array(
            'search_issues' => 'Filter by Issue',
            'selected_issues' => 'Currently filtering data by the following issue',
            'category' => 'Category Filter',
            'field'     => 'Fields',
            'xfield'    => 'X-Axis',
            'yfield'    => 'Y-Axis',
            'dynamic_form' => array(
                'title' => 'Form Type',
                'select' => ' - Select - '
            ),
            'preview_issue_chart' => 'Preview Charts for Issues',
            'preview_global_chart' => 'Preview Chart for Featured data',
            'generated_fail' => 'Chart cannot be generated based on the selections',
            'need_login' => 'Chart cannot be generated before login.',
            'add_chart_to_issue' => 'Add chart to issue page',
            'add_chart_to_featured_datas' => 'Add chart to Featured Data',
        ),
        'static_fields' => array(
            'project_title' => 'Issue Name',
            'report_title' => 'Report Title',
            'report_description' => 'Report Description',
            'report_lng' => 'Report Longitude',
            'report_lat' => 'Report Latitude',
            'report_createby' => 'Report Created By'
        ),
        'field_for_x' => array(
            'project_title' => 'Issue Name',
            'report_title' => 'Report Title',
            'report_description' => 'Report Description',
            'report_lng' => 'Report Longitude',
            'report_lat' => 'Report Latitude',
            'report_createby' => 'Report Created By',
            'time' => 'Time'
        ),
        'field_for_y' => array(
            'report_count' => 'Report Count'
        )
    ),

    /*
    |--------------------------------------------------------------------------
    | Naming of Citizen SMS
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default naming of ArgoDF citizen SMS
    |
    */

    'citizen_sms' => array(
        'name' => 'Citizen SMS',
        'info' => 'To send a SMS to IIU, text 9696',
        'number' => '9696',
        'description' => 'You will receive a confirmation SMS that your message has been received. Once your message has been moderated it will appear on this page. We take note of all your feedback and will post our responses publically here. However, we will not send individual replies unless in exceptional circumstances.',
        'columns' => array(
            'message_detail' => 'Message Detail',
            'action' => 'Action',
        ),
        'components' => array(
            'from' => 'From',
            'on' => 'on',
            'of' => 'of',
            'reply_from' => 'Reply from IIU on',
            'reply' => 'Reply',
            'send' => 'Send',
            'delete' => 'Delete',
            'messages' => 'messages'
        ),
    ),

    /*
    |--------------------------------------------------------------------------
    | Naming of Voice Survey (IVR)
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default naming of Survey page
    |
    */
    'survey' => array(
        'name' => 'Citizen VOICE',
        'info' => '',
        'number' => '',
        'description' => 'Your connection will be disconnected and you will immediately receive a call from this number. You will be asked to introduce yourself and to convey your issue. We will take note of your feedback and might coordinate with you further to obtain more details on the issue. hank you.',
    ),

    /*
    |--------------------------------------------------------------------------
    | Naming of User Management
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default naming of ArgoDF User Management
    |
    */

    'user_management' => array(
        'user_role_hierarchy' => array(
            'name' => 'User Role Hierarchy',
            'public' => array(
                'name' => 'Public',
                'description' => 'Accesses to limited information'
            ),
            'reporter' => array(
                'name' => 'Reporter',
                'description' => 'Collects and reports data'
            ),
            'coordinator' => array(
                'name' => 'Coordinator',
                'description' => 'Validates all the data collected by reporters'
            ),
            'admin' => array(
                'name' => 'Admin',
                'description' => 'Adds and verifies coordinators'
            ),
            'see_user_roles' => 'See user roles',
            'got_it' => 'Got it'
        ),
        'columns' => array(
            'username' => 'Username',
            'email' => 'Email',
            'password' => 'Password',
            'user_type' => 'User Type',
        ),
        'components' => array(
            'add_user' => 'Add User',
            'view_more' => 'View',
            'edit' => 'Edit',
            'delete' => 'Delete',
            'clear' => 'Clear',
            'first' => 'First',
            'last' => 'Last',
            'previous' => 'Previous',
            'next' => 'Next',
            'user_type' => 'User Type',
            'search_users' => 'Search users'
        )
    ),

    /*
    |--------------------------------------------------------------------------
    | Naming of Explore
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default naming of ArgoDF User Management
    |
    */

    'explore' => array(
        'loading_projects' => 'Loading Issues...',
        'search_bar' => array(
            'search_projects_by_title' => 'Search issues by title',
            'project' => 'Issues',
            'reporter_location' => 'Reporter',
            'tracker' => 'Trackers',
            'citizen_report' => 'Issues from Citizen',
            'map_options' => 'Map Options',
            'region' => 'County',
            'district' => 'District',
            'more' => 'More',
        ),
    )
);
