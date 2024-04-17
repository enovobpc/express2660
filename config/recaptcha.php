<?php

return [

    'v3' => [
        'public_key'    => env('GOOGLE_RECAPTCHA_PUBLIC_KEY'),
        'private_key'   => env('GOOGLE_RECAPTCHA_PRIVATE_KEY'),
        'minimum_score' => 0.6,
    ]
];

/**
 * Inserir
 */
// <meta name="grecaptcha-key" content="{{ config('recaptcha.v3.public_key')}}">
// <script src="https://www.google.com/recaptcha/api.js?render={{config('recaptcha.v3.public_key')}}"></script>
