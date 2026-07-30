<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * Indicates whether the XSRF-TOKEN cookie should be set on the response.
     *
     * @var bool
     */
    protected $addHttpCookie = true;

    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        'paytm-callback',
        'https://www.zouple.in/paytm-callback',
        'https://zouple.com/paytm-callback',
        'http://www.zouple.in/paytm-callback',
        'http://localhost/zouple/paytm-callback',
    ];
}
