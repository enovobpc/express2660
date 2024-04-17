<?php

return [

    /*
    |--------------------------------------------------------------------------
    | PDO Fetch Style
    |--------------------------------------------------------------------------
    |
    | By default, database results will be returned as instances of the PHP
    | stdClass object; however, you may desire to retrieve records in an
    | array format for simplicity. Here you can tweak the fetch style.
    |
    */

    'fetch' => PDO::FETCH_OBJ,

    /*
    |--------------------------------------------------------------------------
    | Default Database Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the database connections below you wish
    | to use as your default connection for all database work. Of course
    | you may use many connections at once using the Database library.
    |
    */

    'default' => env('DB_CONNECTION', 'mysql'),

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the database connections setup for your application.
    | Of course, examples of configuring each database platform that is
    | supported by Laravel is shown below to make development simple.
    |
    |
    | All database work in Laravel is done through the PHP PDO facilities
    | so make sure you have the driver for your particular database of
    | choice installed on your machine before you begin development.
    |
    */

    'connections' => [

        'sqlite' => [
            'driver' => 'sqlite',
            'database' => env('DB_DATABASE', database_path('database.sqlite')),
            'prefix' => '',
        ],

        'mysql' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST', 'localhost'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'xpto'),
            'username' => env('DB_USERNAME', 'xpto'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => '',
            'strict' => false,
            'engine' => null,
        ],

        'mysql_core' => [
            'driver'    => 'mysql',
            'host'      => env('DB_HOST_CORE', 'localhost'),
            'port'      => env('DB_PORT_CORE', '3306'),
            'database'  => env('DB_DATABASE_CORE', 'forge'),
            'username'  => env('DB_USERNAME_CORE', 'forge'),
            'password'  => env('DB_PASSWORD_CORE', ''),
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
            'strict'    => false,
            'engine'    => null,
        ],

        'mysql_enovo' => [
            'driver'    => 'mysql',
            'host'      => env('DB_HOST_ENOVO', 'localhost'),
            'port'      => env('DB_PORT_ENOVO', '3306'),
            'database'  => env('DB_DATABASE_ENOVO', 'db_enovo_not_configured'),
            'username'  => env('DB_USERNAME_ENOVO', 'db_enovo_not_configured'),
            'password'  => env('DB_PASSWORD_ENOVO', ''),
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
            'strict'    => false,
            'engine'    => null,
        ],

        'mysql_backup' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST_BACKUP', 'localhost'),
            'port' => env('DB_PORT_BACKUP', '3306'),
            'database' => env('DB_DATABASE_BACKUP', 'forge'),
            'username' => env('DB_USERNAME_BACKUP', 'forge'),
            'password' => env('DB_PASSWORD_BACKUP', ''),
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => '',
            'strict' => false,
            'engine' => null,
        ],

        'mysql_website' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST_WEBSITE', 'localhost'),
            'port' => env('DB_PORT_WEBSITE', '3306'),
            'database' => env('DB_DATABASE_WEBSITE', 'forge'),
            'username' => env('DB_USERNAME_WEBSITE', 'forge'),
            'password' => env('DB_PASSWORD_WEBSITE', ''),
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => '',
            'strict' => false,
            'engine' => null,
        ],

        'mysql_logs' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST_LOGS', 'localhost'),
            'port' => env('DB_PORT_LOGS', '3306'),
            'database' => env('DB_DATABASE_LOGS', 'forge'),
            'username' => env('DB_USERNAME_LOGS', 'forge'),
            'password' => env('DB_PASSWORD_LOGS', ''),
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => '',
            'strict' => false,
            'engine' => null,
        ],

        'mysql_fleet' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST_FLEET', 'localhost'),
            'port' => env('DB_PORT_FLEET', '3306'),
            'database' => env('DB_DATABASE_FLEET', 'forge'),
            'username' => env('DB_USERNAME_FLEET', 'forge'),
            'password' => env('DB_PASSWORD_FLEET', ''),
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => '',
            'strict' => false,
            'engine' => null,
        ],

        'mysql_logistic' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST_LOGISTIC', 'localhost'),
            'port' => env('DB_PORT_LOGISTIC', '3306'),
            'database' => env('DB_DATABASE_LOGISTIC', 'forge'),
            'username' => env('DB_USERNAME_LOGISTIC', 'forge'),
            'password' => env('DB_PASSWORD_LOGISTIC', ''),
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => '',
            'strict' => false,
            'engine' => null,
        ],

        'mysql_budgets' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST_BUDGETS', 'localhost'),
            'port' => env('DB_PORT_BUDGETS', '3306'),
            'database' => env('DB_DATABASE_BUDGETS', 'forge'),
            'username' => env('DB_USERNAME_BUDGETS', 'forge'),
            'password' => env('DB_PASSWORD_BUDGETS', ''),
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => '',
            'strict' => false,
            'engine' => null,
        ],

        'mysql_awb' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST_AWB', 'localhost'),
            'port' => env('DB_PORT_AWB', '3306'),
            'database' => env('DB_DATABASE_AWB', 'forge'),
            'username' => env('DB_USERNAME_AWB', 'forge'),
            'password' => env('DB_PASSWORD_AWB', ''),
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => '',
            'strict' => false,
            'engine' => null,
        ],

        'dbActivos24' => array(
            'driver'    => 'sqlsrv',
            'host'      => '213.63.148.82',//'activos24.virtualcloud.pt', // Provide IP address here
            'database'  => 'OnS3_ACT',
            'username'  => 'enovo',
            'password'  => '9RaZqzsV6wSotSHQ',
            'prefix'    => '',
        ),

        'sqlsrv_phc' => array(
            'driver'    => 'sqlsrv',
            'host'      => env('DB_HOST_PHC', 'sem host'),
            'port'      => env('DB_PORT_PHC', '1433'),
            'database'  => env('DB_DATABASE_PHC', 'sem bd'),
            'username'  => env('DB_USERNAME_PHC', 'sem user'),
            'password'  => env('DB_PASSWORD_phc', 'sem password'),
            'prefix'    => '',
        ),

        'mysql_wordpress' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST_WORDPRESS', 'localhost'),
            'port' => env('DB_PORT_WORDPRESS', '3306'),
            'database' => env('DB_DATABASE_WORDPRESS', 'forge'),
            'username' => env('DB_USERNAME_WORDPRESS', 'forge'),
            'password' => env('DB_PASSWORD_WORDPRESS', ''),
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => '',
            'strict' => false,
            'engine' => null,
        ],

        'pgsql' => [
            'driver' => 'pgsql',
            'host' => env('DB_HOST', 'localhost'),
            'port' => env('DB_PORT', '5432'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'schema' => 'public',
            'sslmode' => 'prefer',
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    |
    | This table keeps track of all the migrations that have already run for
    | your application. Using this information, we can determine which of
    | the migrations on disk haven't actually been run in the database.
    |
    */

    'migrations' => 'migrations',

    /*
    |--------------------------------------------------------------------------
    | Redis Databases
    |--------------------------------------------------------------------------
    |
    | Redis is an open source, fast, and advanced key-value store that also
    | provides a richer set of commands than a typical key-value systems
    | such as APC or Memcached. Laravel makes it easy to dig right in.
    |
    */

    'redis' => [
        
        'client' => 'predis',
        
        'cluster' => false,

        'default' => [
            'host' => env('REDIS_HOST', 'localhost'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', 6379),
            'database' => 0,
        ],

    ],

];
