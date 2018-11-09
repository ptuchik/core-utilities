<?php

return [

    /**
     * Prefix to put before translation strings in trans() usage
     */
    'translations_prefix' => 'general',

    /**
     * Default and fallback locales for translations
     */
    'default_locale'      => env('DEFAULT_LOCALE', env('LOCALE', 'en')),
    'fallback_locale'     => env('FALLBACK_LOCALE', 'fallback'),

    /**
     * Application protocol
     */
    'protocol'            => env('PROTOCOL', 'http'),

    /**
     * Storage disks
     */
    'disks'               => [
        'private' => env('STORAGE'),
        'public'  => env('STORAGE').'_public'
    ],

    /**
     * Images folder to use for saving icons in HasIcon trait
     */
    'images_folder'       => 'assets/images',
];
