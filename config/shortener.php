<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Shortener Scoping Mode
    |--------------------------------------------------------------------------
    |
    | - 'literal': Strict role-based scoping (Sales & Manager create, cross-org admin visibility, auth-required redirect).
    | - 'standard': Default SaaS multi-tenant rules (Admin & Member create, public redirect).
    |
    */
    'mode' => env('APP_SPEC_MODE', 'literal'),
];
