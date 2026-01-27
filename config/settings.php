<?php

return [

    'exchange_rate_api_key' => env("EXCHANGE_RATE_API_KEY"),

    'cron' => env("CRON", '*/5 * * * *'),

    'theme_color' => env("THEME_COLOR", 'Red'),

    'github_community_store_repo' => env("COMMUNITY_STORE_GITHUB_REPO", 'https://api.github.com/repos/Cybrarist/Discount-Bandit-Community-Stores/contents/'),
    'github_community_store_gist_base' => env("COMMUNITY_STORE_GITHUB_GIST_REPO", 'https://raw.githubusercontent.com/Cybrarist/Discount-Bandit-Community-Stores/refs/heads/Master/'),

];
