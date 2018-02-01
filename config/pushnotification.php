<?php

return [
  'gcm' => [
      'priority' => 'normal',
      'dry_run' => false,
      'apiKey' => env('GCM_KEY'),
  ],
  'fcm' => [
        'priority' => 'normal',
        'dry_run' => false,
        'apiKey' => env('FCM_KEY'),
  ],
  'apn' => [
      'certificate' => __DIR__ . '/iosCertificates/pushcert.pem',
      'passPhrase' => env('APN_PASSWORD'), //Optional
      // 'passFile' => __DIR__ . '/iosCertificates/yourKey.pem', //Optional
      'dry_run' => true
  ]
];