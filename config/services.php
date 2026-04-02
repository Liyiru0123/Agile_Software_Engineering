<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'gemini' => [
        'api_key' => env('GEMINI_API_KEY'),
        'model' => env('GEMINI_MODEL', 'gemini-2.5-flash'),
        'base_url' => env('GEMINI_BASE_URL', 'https://generativelanguage.googleapis.com/v1beta/models'),
    ],

    'qwen_omni' => [
        'api_key' => env('QWEN_OMNI_API_KEY'),
        'model' => env('QWEN_OMNI_MODEL', 'qwen-omni-turbo'),
        'base_url' => env('QWEN_OMNI_BASE_URL', 'https://dashscope.aliyuncs.com/compatible-mode/v1'),
    ],

    'groq' => [
        'api_key' => env('GROQ_API_KEY'),
        'stt_model' => env('GROQ_STT_MODEL', 'whisper-large-v3-turbo'),
        'base_url' => env('GROQ_BASE_URL', 'https://api.groq.com/openai/v1'),
    ],

    'speaking' => [
        // gemini: native audio multimodal scoring
        // qwen_omni: native audio multimodal scoring (OpenAI-compatible endpoint)
        // ollama: requires STT first
        'provider' => env('SPEAKING_EVAL_PROVIDER', 'gemini'),
    ],

];
