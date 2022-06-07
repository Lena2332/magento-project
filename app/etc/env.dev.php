<?php
return [
    'backend' => [
        'frontName' => 'admin'
    ],
    'remote_storage' => [
        'driver' => 'file'
    ],
    'queue' => [
        'consumers_wait_for_messages' => 1
    ],
    'crypt' => [
        'key' => '1fbf8fb522c87cee093cec77bbfd9a18'
    ],
    'db' => [
        'table_prefix' => 'm2_',
        'connection' => [
            'default' => [
                'host' => 'mysql',
                'dbname' => 'olena_kupriiets_magento_dev',
                'username' => 'olena_kupriiets_magento_dev_user',
                'password' => 'mfj@i23l-OSz:s345:v3+4@cdkswsdf',
                'model' => 'mysql4',
                'engine' => 'innodb',
                'initStatements' => 'SET NAMES utf8;',
                'active' => '1',
                'driver_options' => [
                    1014 => false
                ]
            ]
        ]
    ],
    'resource' => [
        'default_setup' => [
            'connection' => 'default'
        ]
    ],
    'x-frame-options' => 'SAMEORIGIN',
    'MAGE_MODE' => 'production',
    'session' => [
        'save' => 'redis',
        'redis' => [
            'host' => 'redis',
            'port' => '6379',
            'password' => '',
            'timeout' => '2.5',
            'persistent_identifier' => '',
            'database' => '2',
            'compression_threshold' => '2048',
            'compression_library' => 'gzip',
            'log_level' => '4',
            'max_concurrency' => '6',
            'break_after_frontend' => '5',
            'break_after_adminhtml' => '30',
            'first_lifetime' => '600',
            'bot_first_lifetime' => '60',
            'bot_lifetime' => '7200',
            'disable_locking' => '0',
            'min_lifetime' => '60',
            'max_lifetime' => '2592000'
        ]
    ],
    'cache' => [
        'frontend' => [
            'default' => [
                'backend' => 'Magento\\Framework\\Cache\\Backend\\Redis',
                'backend_options' => [
                    'server' => 'redis',
                    'database' => '0',
                    'port' => '6379'
                ],
                'id_prefix' => '69d_'
            ],
            'page_cache' => [
                'backend' => 'Magento\\Framework\\Cache\\Backend\\Redis',
                'backend_options' => [
                    'server' => 'redis',
                    'port' => '6379',
                    'database' => '1',
                    'compress_data' => '0'
                ],
                'id_prefix' => '69d_'
            ]
        ],
        'allow_parallel_generation' => false
    ],
    'lock' => [
        'provider' => 'db',
        'config' => [
            'prefix' => ''
        ]
    ],
    'directories' => [
        'document_root_is_pub' => true
    ],
    'cache_types' => [
        'config' => 1,
        'layout' => 1,
        'block_html' => 1,
        'collections' => 1,
        'reflection' => 1,
        'db_ddl' => 1,
        'compiled_config' => 1,
        'eav' => 1,
        'customer_notification' => 1,
        'config_integration' => 1,
        'config_integration_api' => 1,
        'full_page' => 1,
        'config_webservice' => 1,
        'translate' => 1,
        'vertex' => 1
    ],
    'downloadable_domains' => [
        'olena-kupriiets-magento.local'
    ],
    'install' => [
        'date' => 'Thu, 05 May 2022 08:42:40 +0000'
    ],
    'system' => [
        'default' => [
            'web' => [
                'unsecure' => [
                    'base_url' => 'https://olena-kupriiets-magento-local.allbugs.info/',
                    'base_link_url' => 'https://olena-kupriiets-magento-local.allbugs.info/',
                    'base_static_url' => 'https://olena-kupriiets-magento-local.allbugs.info/static/',
                    'base_media_url' => 'https://olena-kupriiets-magento-local.allbugs.info/media/'
                ],
                'secure' => [
                    'base_url' => 'https://olena-kupriiets-magento-local.allbugs.info/',
                    'base_link_url' => 'https://olena-kupriiets-magento-local.allbugs.info/',
                    'base_static_url' => 'https://olena-kupriiets-magento-local.allbugs.info/static/',
                    'base_media_url' => 'https://olena-kupriiets-magento-local.allbugs.info/media/'
                ]
            ]
        ],
        'websites' => [
            'us_website' => [
                'web' => [
                    'unsecure' => [
                        'base_url' => 'https://olena-kupriiets-magento-us.allbugs.info/',
                        'base_link_url' => 'https://olena-kupriiets-magento-us.allbugs.info/',
                        'base_static_url' => 'https://olena-kupriiets-magento-us.allbugs.info/static/',
                        'base_media_url' => 'https://olena-kupriiets-magento-us.allbugs.info/media/'
                    ],
                    'secure' => [
                        'base_url' => 'https://olena-kupriiets-magento-us.allbugs.info/',
                        'base_link_url' => 'https://olena-kupriiets-magento-us.allbugs.info/',
                        'base_static_url' => 'https://olena-kupriiets-magento-us.allbugs.info/static/',
                        'base_media_url' => 'https://olena-kupriiets-magento-us.allbugs.info/media/'
                    ]
                ]
            ]
        ]
    ],
    'http_cache_hosts' => [
        [
            'host' => '127.0.0.1',
            'port' => '6081'
        ]
    ]
];
