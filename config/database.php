<?php

return [

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

        'mysqlapi' => [
            'driver' => 'mysql',
            'host' => 'localhost',
            'port' => '3306',
            'database' => 'api_market_place',
            'username' => 'api-admin',
            'password' => 'Qwer1212',
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => '',
            'strict' => false,
            'engine' => null,
        ],
		
         'pgsql' => [

            'driver' => 'pgsql',
            'host' => '127.0.0.1',
            'port' =>  '5432',
            'database' => 'precluck_market_place',
            'username' => env('DB_USERNAME', 'market'),
            'password' => env('DB_PASSWORD', 'katamaran_boiler'),


            'charset' => 'utf8',
            'prefix' => '',
            'schema' => 'public',

        ],
        'advertise' => [
            'driver' => 'pgsql',
            'host' => '127.0.0.1',
            'port' =>  '5432',
            'database' => 'advertise',
            'username' => env('DB_USERNAME', 'market'),
            'password' => env('DB_PASSWORD', 'katamaran_boiler'),

            'charset' => 'utf8',
            'prefix' => '',
            'schema' => 'public',
        
        ],
        'pgstatistic' => [
            'driver' => 'pgsql',
            'host' => '127.0.0.1',
            'port' =>  '5432',
            'database' => 'statistic_market_place',
            'username' => env('DB_USERNAME', 'market'),
            'password' => env('DB_PASSWORD', 'katamaran_boiler'),


            'charset' => 'utf8',
            'prefix' => '',
            'schema' => 'public',
        ],
        'pgstatistic_new' => [
            'driver' => 'pgsql',
            'host' => '127.0.0.1',
            'port' =>  '5432',
            'database' => 'statistic_product_market_place',
            'username' => env('DB_USERNAME', 'market'),
            'password' => env('DB_PASSWORD', 'katamaran_boiler'),


            'charset' => 'utf8',
            'prefix' => '',
            'schema' => 'public',
        ],
        'pgstatistic_next' => [
            'driver' => 'pgsql',
            'host' => '127.0.0.1',
            'port' =>  '5432',
            'database' => 'stat_next_product',
            'username' => env('DB_USERNAME', 'market'),
            'password' => env('DB_PASSWORD', 'katamaran_boiler'),

            'charset' => 'utf8',
            'prefix' => '',
            'schema' => 'public',
        ],
        'product_next' => [
            'driver' => 'pgsql',
            'host' => '127.0.0.1',
            'port' =>  '5432',
            'database' => 'next_product',
            'username' => env('DB_USERNAME', 'market'),
            'password' => env('DB_PASSWORD', 'katamaran_boiler'),

            'charset' => 'utf8',
            'prefix' => '',
            'schema' => 'public',
        ],


        'pgstatistic_new_video' => [
            'driver' => 'pgsql',
            'host' => '127.0.0.1',
            'port' =>  '5432',
            'database' => 'statistic_video_market_place',
            'username' => env('DB_USERNAME', 'market'),
            'password' => env('DB_PASSWORD', 'katamaran_boiler'),
            'charset' => 'utf8',
            'prefix' => '',
            'schema' => 'public',
        ],

        'pg_product' => [
            'driver' => 'pgsql',
            'host' => '127.0.0.1',
            'port' =>  '5432',
            'database' => 'product_market_place',
            'username' => env('DB_USERNAME', 'market'),
            'password' => env('DB_PASSWORD', 'katamaran_boiler'),


            'charset' => 'utf8',
            'prefix' => '',
            'schema' => 'public',
        ],

        'pgstatistic_teaser' => [
            'driver' => 'pgsql',
            'host' => '127.0.0.1',
            'port' =>  '5432',
            'database' => 'statistic_teaser_market_place',
            'username' => env('DB_USERNAME', 'market'),
            'password' => env('DB_PASSWORD', 'katamaran_boiler'),

            'charset' => 'utf8',
            'prefix' => '',
            'schema' => 'public',
        ],


	'cluck' => [
            'driver' => 'pgsql',
            'host' => '127.0.0.1',
            'port' =>  '5432',
            'database' => 'cluck_market_place',
            'username' => env('DB_USERNAME', 'market'),
            'password' => env('DB_PASSWORD', 'katamaran_boiler'),


            'charset' => 'utf8',
            'prefix' => '',
            'schema' => 'public',
        ],
	'videotest' => [
            'driver' => 'pgsql',
            'host' => '127.0.0.1',
            'port' =>  '5432',
            'database' => 'videotest',
            'username' => env('DB_USERNAME', 'market'),
            'password' => env('DB_PASSWORD', 'katamaran_boiler'),

            'charset' => 'utf8',
            'prefix' => '',
            'schema' => 'public',
        ],
		
	'obmenneg' => [
            'driver' => 'pgsql',
            'host' => '127.0.0.1',
            'port' =>  '5432',
            'database' => 'obmenneg',
            'username' => env('DB_USERNAME', 'market'),
            'password' => env('DB_PASSWORD', 'katamaran_boiler'),

            'charset' => 'utf8',
            'prefix' => '',
            'schema' => 'public',
        ],
		
	'crypto' => [
            'driver' => 'pgsql',
            'host' => '127.0.0.1',
            'port' =>  '5432',
            'database' => 'crypto',
            'username' => env('DB_USERNAME', 'market'),
            'password' => env('DB_PASSWORD', 'katamaran_boiler'),

            'charset' => 'utf8',
            'prefix' => '',
            'schema' => 'public',
        ],
		
	'report' => [
            'driver' => 'pgsql',
            'host' => '127.0.0.1',
            'port' =>  '5432',
            'database' => 'report',
            'username' => env('DB_USERNAME', 'market'),
            'password' => env('DB_PASSWORD', 'katamaran_boiler'),

            'charset' => 'utf8',
            'prefix' => '',
            'schema' => 'public',
        ],
	
	'videotest2' => [
            'driver' => 'pgsql',
            'host' => '127.0.0.1',
            'port' =>  '5432',
            'database' => 'videotest',
            'username' => env('DB_USERNAME', 'market'),
            'password' => env('DB_PASSWORD', 'katamaran_boiler'),

            'charset' => 'utf8',
            'prefix' => '',
            'schema' => 'public',
        ],
	'video_' => [
            'driver' => 'pgsql',
            'host' => '127.0.0.1',
            'port' =>  '5432',
            'database' => 'video_',
            'username' => env('DB_USERNAME', 'market'),
            'password' => env('DB_PASSWORD', 'katamaran_boiler'),

            'charset' => 'utf8',
            'prefix' => '',
            'schema' => 'public',
        ],


        'sqlsrv' => [
            'driver' => 'sqlsrv',
            'host' => env('DB_HOST', 'localhost'),
            'port' => env('DB_PORT', '1433'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8',
            'prefix' => '',
        ],
		'api_market' => [
            'driver' => 'pgsql',
            'host' => '127.0.0.1',
            'port' =>  '5432',
            'database' => 'api_market_place',
            'username' => env('DB_USERNAME', 'market'),
            'password' => env('DB_PASSWORD', 'katamaran_boiler'),


            'charset' => 'utf8',
            'prefix' => '',
            'schema' => 'public',
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

        'default' => [
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', 6379),
            'database' => 0,
        ],

    ],

];
