<?php declare(strict_types = 1);

require_once '../../vendor/autoload.php';

use jotaa\core\FrankyCore;

$franky = FrankyCore::getApp(
    [
        'database' => 'expoin',
        'username' => 'homestead',
        'password' => 'secret',
        'host'     => '192.168.10.10',
        'type'     => 'mysql'
    ],
    [
        'rootPath' => '/home/jacobopus/Documentos/repos/Homestead/code/app/testamp',
        'viewPath' => '/home/jacobopus/Documentos/repos/Homestead/code/app/testamp/views',
        'assetPath' => '/home/jacobopus/Documentos/repos/Homestead/code/app/testamp/assets',
        'cssPath'  => '/home/jacobopus/Documentos/repos/Homestead/code/app/testamp/assets/css',
        'logPath'  => __DIR__ . '/log.log',
    ]
);

$franky->route('GET /', function () use ($franky) {
    var_dump($franky->config('fuck'));
});

$franky->start();
