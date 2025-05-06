<?php

return [
    'secret' => env('UPLOADTHING_SECRET'),
    'app_id' => env('UPLOADTHING_APP_ID'),
    'region_alias' => env('UPLOADTHING_REGION_ALIAS', 'sea1'),
];
