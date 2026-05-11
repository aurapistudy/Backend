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
        'image_model' => env('GEMINI_IMAGE_MODEL', 'gemini-2.5-flash-image'),
        'text_model' => env('GEMINI_TEXT_MODEL', 'gemini-2.5-flash-lite'),
    ],

    /** Rangkuman teks bab: gemini | huggingface */
    'bab_summary' => [
        'text_provider' => strtolower((string) env('SUMMARY_TEXT_PROVIDER', 'gemini')),
    ],

    'huggingface' => [
        'api_token' => env('HF_API_TOKEN'),
        'image_model' => env('HF_IMAGE_MODEL', 'black-forest-labs/FLUX.1-schnell'),
        /** Model teks untuk rangkuman bab (Inference API). */
        'summary_text_model' => env('HF_SUMMARY_TEXT_MODEL', 'Qwen/Qwen2.5-3B-Instruct'),
        'summary_text_max_new_tokens' => env('HF_SUMMARY_TEXT_MAX_NEW_TOKENS', 900),
        /** Model khusus poster rangkuman; kosongkan agar memakai image_model (mis. cover buku). */
        'summary_poster_model' => env('HF_SUMMARY_POSTER_MODEL'),
        /** Langkah inferensi poster; kosong = otomatis (schnell: 4, selain itu: 22). */
        'summary_poster_inference_steps' => env('HF_SUMMARY_POSTER_INFERENCE_STEPS'),
        /** Guidance poster; kosong = 3.5 */
        'summary_poster_guidance_scale' => env('HF_SUMMARY_POSTER_GUIDANCE_SCALE'),
        'base_url' => env('HF_BASE_URL', 'https://router.huggingface.co/hf-inference/models'),
    ],

];
