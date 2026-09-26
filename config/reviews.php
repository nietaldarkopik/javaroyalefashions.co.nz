<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Review invitation lifetime
    |--------------------------------------------------------------------------
    | How many days an emailed review link stays usable. Resending an
    | invitation issues a fresh link and restarts this window.
    */
    'invitation_expiry_days' => (int) env('REVIEW_INVITATION_EXPIRY_DAYS', 30),

    /*
    |--------------------------------------------------------------------------
    | Field limits
    |--------------------------------------------------------------------------
    */
    'name_max' => 80,
    'title_max' => 120,
    'comment_min' => 5,
    'comment_max' => 2000,

    /*
    |--------------------------------------------------------------------------
    | Approved reviews shown per page on a product page
    |--------------------------------------------------------------------------
    */
    'per_page' => 5,

];
