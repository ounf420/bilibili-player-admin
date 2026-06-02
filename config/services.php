<?php

return [
    'socialite' => [
        'api_url' => env('SOCIALITE_API_URL', 'https://login.cxavn.cn'),
        'appid' => env('SOCIALITE_APPID', ''),
        'appkey' => env('SOCIALITE_APPKEY', ''),
        'callback' => env('SOCIALITE_CALLBACK', 'https://dem.viesta.cn/api/socialite/callback'),
        'platforms' => [
            'qq' => true,
            'wx' => true,
            'alipay' => true,
            'sina' => true,
            'baidu' => false,
            'douyin' => true,
            'huawei' => false,
            'xiaomi' => false,
            'microsoft' => false,
            'feishu' => false,
            'dingtalk' => false,
            'gitee' => false,
            'github' => true,
        ],
    ],
];
