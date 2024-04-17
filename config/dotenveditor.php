<?php
/**
 * Created by PhpStorm.
 * User: Fabian
 * Date: 12.05.16
 * Time: 07:24
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Path configuration
    |--------------------------------------------------------------------------
    |
    | Change the paths, so they fit your needs
    |
     */
    'pathToEnv'       => base_path('.env'),
    'backupPath'      => resource_path('backups/dotenv-editor/'),
    'filePermissions' => env('FILE_PERMISSIONS', 0755),

    /*
    |--------------------------------------------------------------------------
    | GUI-Settings
    |--------------------------------------------------------------------------
    |
    | Here you can set the different parameter for the view, where you can edit
    | .env via a graphical interface.
    |
    | Comma-separate your different middlewares.
    |
     */

    // Activate or deactivate the graphical interface
    'activated' => true,
    //'template'  => 'admin.layouts.master',
    'template'  => 'dotenv-editor::master',
    'overview'  => 'dotenv-editor::overview',

    // Config route group
    'route' => [
        'namespace'  => 'Brotzka\DotenvEditor\Http\Controllers',
        'prefix'     => 'admin/env',
        'as'         => 'admin.env.',
        'middleware' => ['web', 'guest'],
    ],
];
