<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Verifikasi Email & Nomor Telepon
    |--------------------------------------------------------------------------
    |
    | Set false untuk menonaktifkan verifikasi selama pengujian. Saat nonaktif,
    | email dan nomor telepon dianggap terverifikasi dan tidak ada kode yang
    | dikirim. Aktifkan kembali dengan CONTACT_VERIFICATION_ENABLED=true.
    |
    */
    'enabled' => env('CONTACT_VERIFICATION_ENABLED', false),
];
