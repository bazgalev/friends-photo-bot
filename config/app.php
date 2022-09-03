<?php

// test
return [
    'telegram' => [
        'token' => '',

        // Команды, выполняющиеся по расписанию будут взаимодействовать с указанным чатом
        'chat_id' => -1,

        // Бот взаимодействует с чатами из белого листа
        'whitelist_chats' => [
            1,
            2
        ],
    ],

    'logging' => [
        'telegram' => [
            'token' => '',
            'chat_id' => 1,
        ],
    ],

    'vk' => [
        'access_token' => '',
        'owner_id' => 1,
        'quote_topic_id' => 1,
    ],
];