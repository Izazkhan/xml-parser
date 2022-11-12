<?php

return [
    
    // OAuth
    // 'client_id'        => env('GOOGLE_CLIENT_ID', ''),
    // 'client_secret'    => env('GOOGLE_CLIENT_SECRET', ''),
    // 'redirect_uri'     => env('GOOGLE_REDIRECT', ''),
    'access_type'      => 'online',
    'approval_prompt'  => 'auto',
    'prompt'           => 'consent', //"none", "consent", "select_account" default:none
    
    'scopes'           => [\Google\Service\Sheets::DRIVE, \Google\Service\Sheets::SPREADSHEETS],
    'service' => [
        'file'    => \Phar::running() ? '/storage/google/creds.json' : storage_path(env('GOOGLE_SERVICE_ACCOUNT_JSON_LOCATION', 'google/creds.json')),
        'enable'  => true
    ],
    'sheet' => [
        'id' => '11JFLFFnm_vO2xnTxoXYjJppuolfrL91sU3B-9YDyMg4',
        'name' => 'Sheet1',
    ]
];
?>