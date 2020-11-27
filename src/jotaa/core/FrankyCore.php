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
final class FrankyCore
{
    public const ENV_PROD = 'PRODUCTION';
    public const ENV_DEV  = 'DEVELOPMENT';
    public const ENV_QA   = 'QASSESTMENT';

    public const CORE_ALLOWED_ENVIRONMENTS = [self::ENV_PROD, self::ENV_DEV, self::ENV_QA];

    private \AltoRouter $router;
    private \Pop\Db\Adapter\AbstractAdapter $db;
    private CoreMailHandlerPhpMailer $mailer;
    private string $environment;
    private array $config;
    private \Pop\Session\Session $session;
    private \Monolog\Logger $log;
    private $whoops;


    public function __construct(array $dbOptions, array $config, string $environment = self::ENV_DEV)
    {
        $this->router = new \AltoRouter();
        $this->db = \Pop\Db\Db::connect('mysql', $dbOptions);
        \Pop\Db\Record::setDb($this->db);

        $this->mailer = new CoreMailHandlerPhpMailer();
        $this->environment = $environment;
        $this->config = $config;
        $this->session = \Pop\Session\Session::getInstance();

        $this->log = new Logger('name');
        $this->log->pushHandler(
            new StreamHandler(
                $this->config('logPath'),
                Logger::INFO
            )
        );

        $this->mailer->registerMailHandler($this->log);

        $whoops = new \Whoops\Run;
        $sself = $this;
        $whoops->pushHandler(
            function ($ex) use ($sself, $whoops) {
                $sself->log->error($ex);
                if ($sself->environment === self::ENV_DEV) {
                    $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
                    $html = $whoops->handleException($ex);
                    echo $html;
                    return  \Whoops\Handler\Handler::DONE;
                }
            }
        );
        $whoops->register();
        $this->whoops = $whoops;
    }


    public function __get(string $property = '')
    {
        if (property_exists($this, $property)) {
            if ($property === 'config') {
                throw new \jotaa\core\core_exceptions\CoreShouldUseOtherException('You should use getConfig method');
            }
            return $this->{$property};
        }
        throw new CoreUnexistentPropertyException("Undefined property {$property}");
    }


    public function config(string $key = '')
    {
        if (empty($key)) {
            return $this->config;
        }

        if (array_key_exists($key, $this->config)) {
            return $this->config[$key];
        }

        throw new \OutOfBoundsException("key {$key} doesnt exists in FrankyCore::\$config");
    }


    public function rootPath()
    {
        return $this->config('rootPath');
    }


    public function viewPath()
    {
        return $this->config('viewPath');
    }


    public function assetPath()
    {
        return $this->config('assetPath');
    }


    public function cssPath()
    {
        return $this->config('cssPath');
    }
}
