<?php declare(strict_types = 1);

namespace jotaa\core;

use jotaa\core\core_classes\CoreMailHandlerPhpMailer;
use jotaa\core\core_exceptions\CoreFileDoesntExistsException;
use jotaa\core\core_exceptions\CoreUnexistentPropertyException;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

/**
 * FrankyCore
 *
 * This class represents the application api.
 * Stores reference to core components which will be used along
 * the app.
 *
 * @Author  Andrés Reyes a.k.a. Undercoder a.k.a. Jacobopous
 * @Version 0.1.0
 */
class FrankyCore
{
    public const ENV_PROD = 'PRODUCTION';
    public const ENV_DEV  = 'DEVELOPMENT';
    public const ENV_QA   = 'QASSESTMENT';

    public const CORE_ALLOWED_ENVIRONMENTS = [self::ENV_PROD, self::ENV_DEV, self::ENV_QA];

    public static function getApp(
        array $dbOptions,
        array $config,
        string $environment = self::ENV_DEV
    ) : \flight\Engine {

        

        $franky = new \flight\Engine();
        $db = \Pop\Db\Db::connect('mysql', $dbOptions);
        \Pop\Db\Record::setDb($db);

        $mailer = new CoreMailHandlerPhpMailer();

        $franky->map(
            'mailer',
            function () use ($mailer) {
                return $mailer;
            }
        );

        $franky->map(
            'db',
            function () use ($db) {
                return $db;
            }
        );

        $franky->map(
            'environment',
            function () use ($environment) {
                return $environment;
            }
        );

        $franky->map(
            'config',
            function (string $key = '') use ($config) {
                if (empty($key)) {
                    return $config;
                }
                if (array_key_exists($key, $config)) {
                    return $config[$key];
                }
                throw new \OutOfBoundsException("Undefined config key {$key}");
            }
        );

        assert(method_exists($franky, 'config'));

        $franky->map(
            'rootPath',
            function () use ($franky) {
                assert(method_exists($franky, 'config'));
                return $franky->config('rootPath');
            }
        );

        $franky->map(
            'viewPath',
            function () use ($franky) {
                return $franky->config('viewPath');
            }
        );

        $franky->map(
            'assetPath',
            function () use ($franky) {
                return $franky->config('assetPath');
            }
        );

        $franky->map(
            'cssPath',
            function () use ($franky) {
                return $franky->config('cssPath');
            }
        );

        $franky->map(
            'merge',
            function (array $m1, array $m2) {
                return self::merge($m1, $m2);
            }
        );

        $franky->map(
            'session',
            function () {
                return \Pop\Session\Session::getInstance();
            }
        );

        $log = new Logger('name');
        $log->pushHandler(
            new StreamHandler(
                $franky->config('logPath'),
                Logger::INFO
            )
        );

        $franky->map('log', function () use ($log) {
            return $log;
        });

        $whoops = new \Whoops\Run;
        $whoops->allowQuit(false);
        $whoops->writeToOutput(false);
        $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
        $whoops->register();

        $franky->map('whoops', function () use ($whoops) {
            return $whoops;
        });

        // create a log channel
        \Flight::set('flight.log_errors', true);

        $mailer->registerMailHandler($log);
        $franky->map('error', function ($ex) use ($franky) {
            $html = $franky->whoops()->handleException($ex);
            $franky->log()->error($ex);

            if ($franky->environment() === self::ENV_DEV) {
                echo $html;
            }
        });

        return $franky;
    }

    private static function merge(array $m, array $m2)
    {
        return array_merge($m, $m2);
    }
}
