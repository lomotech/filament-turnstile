<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Turnstile Site Key
    |--------------------------------------------------------------------------
    |
    | Your Cloudflare Turnstile site key from the Cloudflare dashboard.
    |
    */
    'key' => env('TURNSTILE_SITE_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Turnstile Secret Key
    |--------------------------------------------------------------------------
    |
    | Your Cloudflare Turnstile secret key for server-side verification.
    |
    */
    'secret' => env('TURNSTILE_SECRET_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Reset Event Name
    |--------------------------------------------------------------------------
    |
    | The Livewire event name used to reset the Turnstile widget.
    |
    */
    'reset_event' => env('TURNSTILE_RESET_EVENT', 'reset-captcha'),

    /*
    |--------------------------------------------------------------------------
    | HTTP Retry Configuration
    |--------------------------------------------------------------------------
    |
    | Configure how many times and how long to wait between retries
    | when verifying tokens with the Cloudflare API.
    |
    */
    'retry_times' => env('TURNSTILE_RETRY_TIMES', 3),
    'retry_delay' => env('TURNSTILE_RETRY_DELAY', 100),

    /*
    |--------------------------------------------------------------------------
    | HTTP Timeout
    |--------------------------------------------------------------------------
    |
    | Maximum number of seconds to wait for a response from Cloudflare.
    |
    */
    'timeout' => env('TURNSTILE_TIMEOUT', 10),
];
