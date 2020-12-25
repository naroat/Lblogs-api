<?php

return [
    /**
     * 驱动: 目前支持本地(local)和阿里云OSS(aliyun)
     */
    'drive' => env('UPLOAD_DRIVE', 'local'),
];
