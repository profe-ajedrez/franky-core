<?php declare(strict_types = 1);

require_once '../../vendor/autoload.php';

use jotaa\core\FrankyCore;

$franky = new FrankyCore(
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

$franky->router()->setBasePath('/fcoregt/tests/core');
$franky->router()->map(
    'GET',
    '/',
    function() use ($franky) {
        var_dump($franky->config());
    }
);

$match = $franky->router()->match();

// call closure or throw 404 status
if( is_array($match) && is_callable( $match['target'] ) ) {
    call_user_func_array( $match['target'], $match['params'] );
} else {
    // no route was matched
    header( $_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
}
