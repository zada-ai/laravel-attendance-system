<?php

return [
    'enabled' => env('WHATSAPP_ENABLED', false),
    'twilio_sid' => env('TWILIO_SID', env('TWILIO_ACCOUNT_SID')),
    'twilio_token' => env('TWILIO_AUTH_TOKEN'),
    'twilio_whatsapp_from' => env('TWILIO_WHATSAPP_FROM', 'whatsapp:+14155238886'),
];
