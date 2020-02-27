<?php

return [
    'class' => 'yii\swiftmailer\Mailer',
    'useFileTransport' => false,
    'transport' => [
        'class' => 'Swift_SmtpTransport',
        'host' => 'smtp.example.com',
        'username' => '',
        'password' => '',
        'port' => '587',
        'encryption' => 'ssl',
    ],
];