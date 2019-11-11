<?php

return array(
    'defaults' => array(
        'phpmailer' => array(
            'Mailer' => 'smtp',
            'SMTPAuth' => true,
            'SMTPSecure ' => 'tls',
            'Host' => 'tls://smtp.gmail.com',
            'Port' => 587,
            'Username' => 'vcctestemail123@gmail.com',
            'Password' => 'melody@123',
            'Timeout' => 30, // 30 seconds
        ),
        'wordwrap' => 0
    )
);
