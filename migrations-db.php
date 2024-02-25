<?php

$app = require __DIR__.'/bootstrap/app.php';
$app->register(Persium\Doctrine\Providers\DoctrineServiceProvider::class);

return [
    'host' => config('database.connections.pgsql.host', 'localhost'),
    'driver'   => config('database.connections.pgsql.driver', 'pdo_mysql'),
    'user'     => config('database.connections.pgsql.username', 'root'),
    'password' => config('database.connections.pgsql.password', 'root'),
    'dbname'   => config('database.connections.pgsql.database', 'iam'),
];
