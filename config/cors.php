<?php

return [

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => ['http://192.168.8.33:9000','http://localhost:9001','http://localhost:9000', 'http://192.168.8.80:9000','http://192.168.8.80:8000','http://192.168.8.11:8000','http://192.168.8.11:9000','http://192.168.50.98:8000','http://192.168.50.98:9000', 'http://10.0.1.23:89','http://10.0.1.23:90'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

];
