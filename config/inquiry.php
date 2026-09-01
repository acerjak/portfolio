<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Inquiry Notification Recipient
    |--------------------------------------------------------------------------
    |
    | Inquiries submitted through the contact form are sent to this address. Override it
    | by setting INQUIRY_TO_EMAIL in your .env file.
    |
    */

    'to_address' => env('INQUIRY_TO_EMAIL', 'amandacojerean@gmail.com'),

];
