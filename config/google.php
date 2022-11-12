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
        'file'    => storage_path(env('GOOGLE_SERVICE_ACCOUNT_JSON_LOCATION'), 'google/creds.json'),
        'enable'  => env('GOOGLE_SERVICE_ENABLED', true)
    ],
    'sheet' => [
        'id' => env('GOOGLE_SERVICE_SHEET_ID'),
        'name' => env('GOOGLE_SERVICE_SHEET_NAME'),
    ]
];
?>