<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Git Auto Pull Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for the Git auto pull feature
    |
    | Created: 2025-05-17 07:48:58
    | Author: wichaksono
    |
    */

    'repositories' => [
        // List of repositories to auto-pull
        // 'repo-name' => [
        //     'path' => '/path/to/repo',
        //     'branch' => 'main',  // optional
        //     'credentials' => [    // optional for private repos
        //         'username' => '',  // Set via services config
        //         'token' => ''      // Set via services config
        //     ]
        // ]
    ],

    'schedule' => [
        // Instead of using env() directly, these should be set in config/services.php
        'enabled' => false,
        'frequency' => 'hourly', // hourly, daily, custom
        'cron' => '0 * * * *',  // custom cron expression
    ],

    'logging' => [
        'enabled' => true,
        'channel' => 'git-operations'
    ]
];
