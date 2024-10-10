<?php

declare(strict_types=1);

use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\Repositories\MemoryRepository;

return [
    'default_connection' => 'default',

    'connections' => [
        'default' => [
            'host' => env('MQTT_HOST', '077e7645a4a5475fb518733947a92af2.s1.eu.hivemq.cloud'),
            'port' => env('MQTT_PORT', 8883),
            'protocol' => MqttClient::MQTT_3_1,
            'client_id' => env('MQTT_CLIENT_ID', 'your_client_id'), // Gantilah 'your_client_id' sesuai kebutuhan
            'use_clean_session' => env('MQTT_CLEAN_SESSION', true),
            'enable_logging' => env('MQTT_ENABLE_LOGGING', true),
            'log_channel' => env('MQTT_LOG_CHANNEL', null),
            'repository' => MemoryRepository::class,
            'connection_settings' => [
                'tls' => [
                    'enabled' => true, // SSL/TLS harus diaktifkan
                    'allow_self_signed_certificate' => false,
                    'verify_peer' => true,
                    'verify_peer_name' => true,
                ],
                'auth' => [
                    'username' => env('MQTT_AUTH_USERNAME', 'aismail'), // Gantilah sesuai konfigurasi
                    'password' => env('MQTT_AUTH_PASSWORD', 'P@ssw0rd123'), // Gantilah sesuai konfigurasi
                ],
                'last_will' => [
                    'topic' => env('MQTT_LAST_WILL_TOPIC'),
                    'message' => env('MQTT_LAST_WILL_MESSAGE'),
                    'quality_of_service' => env('MQTT_LAST_WILL_QUALITY_OF_SERVICE', 0),
                    'retain' => env('MQTT_LAST_WILL_RETAIN', false),
                ],
                'connect_timeout' => env('MQTT_CONNECT_TIMEOUT', 60),
                'socket_timeout' => env('MQTT_SOCKET_TIMEOUT', 5),
                'keep_alive_interval' => env('MQTT_KEEP_ALIVE_INTERVAL', 10),
            ],
        ],
    ],
];
