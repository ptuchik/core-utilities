<?php

return [

    /**
     * Prefix to put before translation strings in trans() usage
     */
    'translations_prefix' => 'general',

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
