<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Mail Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used for various alert
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */

    'registration.subject' => 'Registrasi akun di ' . config('app.name'),
    'password_reset.subject' => 'Reset Password akun di ' . config('app.name'),
    'forgot_password.subject' => 'Lupa Password akun di ' . config('app.name'),
    'invoice_created.subject' => 'Selesaikan pembayaran anda di ' . config('app.name'),
    'invoice_paid.subject' => 'Pembayaran anda di ' . config('app.name') . ' telah berhasil',
    'invoice_expired.subject' => 'Tagihan anda di ' . config('app.name') . ' telah expired',
    'invoice_preorder.subject' => 'Pelanggan telah membeli produk Preorder!',
    'license_send.subject' => 'Lisensi Produk :product Anda',
    'pin_rekening' => 'Silahkan cek email anda untuk konfirmasi email',
    'rekening_number'=>'Silahkan cek email anda untuk konfirmasi penambahan nomor rekening'
];
