<?php

return [

    /*
    |--------------------------------------------------------------------------
    | AI Driver
    |--------------------------------------------------------------------------
    |
    | Available drivers:
    |   offline  - keyword-based matching, no external calls (always works)
    |   openai   - uses the OpenAI chat completions API
    |
    */

    'driver' => env('AI_DRIVER', 'offline'),

    'openai' => [
        'api_key' => env('OPENAI_API_KEY', ''),
        'model' => env('OPENAI_MODEL', 'gpt-4o-mini'),
        'base_url' => env('OPENAI_BASE_URL', 'https://api.openai.com/v1'),
        'timeout' => 30,
    ],
];