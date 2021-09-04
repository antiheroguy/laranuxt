<?php

return [
    'oauth' => true,
    'permissions' => false,
    'short_lived_token_lifetime' => env('SHORT_LIVED_TOKEN_LIFETIME', 60),
    'long_lived_token_lifetime' => env('LONG_LIVED_TOKEN_LIFETIME', 2592000),
];
