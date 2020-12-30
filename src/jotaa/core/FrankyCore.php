<?php declare(strict_types = 1);

namespace jotaa\core;

use jotaa\core\core_interfaces\CoreHasBehaviorInterface;
use jotaa\core\core_interfaces\CoreBehaviorInterface;
use jotaa\core\core_exceptions\CoreUndefinedBehaviorException;
use jotaa\core\core_classes\CoreMailHandlerPhpMailer;
use Pop\Db\Adapter\AbstractAdapter;
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
 */
final class FrankyCore implements CoreHasBehaviorInterface
{
    public const ENV_PROD = 'PRODUCTION';
    public const ENV_DEV  = 'DEVELOPMENT';
    public const ENV_QA   = 'QASSESTMENT';
    public const CORE_ALLOWED_ENVIRONMENTS = [self::ENV_PROD, self::ENV_DEV, self::ENV_QA];

    /**
     * @var \AltoRouter
     */
    private \AltoRouter $router;
    /**
     * @var AbstractAdapter
     */
    private AbstractAdapter $db;
    /**
     * @var CoreMailHandlerPhpMailer
     */
    private CoreMailHandlerPhpMailer $mailer;
    /**
     * @var string
     */
    private string $environment;
    /**
     * @var array<string>
     */
    private array $config;
    /**
     * @var Logger
     */
    private \Monolog\Logger $log;
    /**
     * @var \Whoops\Run
     */
    private $whoops;


    /**
     * @var CoreBehaviorInterface[] $behaviors
     */
    private array $behaviors = [];


    /**
     * FrankyCore constructor.
     * @param array $dbOptions
     * @param array<string> $config
     * @param string $environment
     * @throws \Pop\Db\Exception
     */
    public function __construct(array $dbOptions, array $config, string $environment = self::ENV_DEV)
    {
        $this->router = new \AltoRouter();
        $this->db = \Pop\Db\Db::connect('pdo', $dbOptions);
        \Pop\Db\Record::setDb($this->db);

        $this->mailer = new CoreMailHandlerPhpMailer();
        $this->environment = $environment;
        $this->config = $config;

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


    /**
     * @return \AltoRouter
     */
    public function router()
    {
        return $this->router;
    }

    /**
     * @return AbstractAdapter
     */
    public function db(): AbstractAdapter
    {
        return $this->db;
    }

    /**
     * @return CoreMailHandlerPhpMailer
     */
    public function mailer(): CoreMailHandlerPhpMailer
    {
        return $this->mailer;
    }

    /**
     * @return string
     */
    public function environment(): string
    {
        return $this->environment;
    }

    /**
     * @return Logger
     */
    public function log(): Logger
    {
        return $this->log;
    }

    /**
     * @return \Whoops\Run
     */
    public function whoops(): \Whoops\Run
    {
        return $this->whoops;
    }



    /**
     * @param string $key
     * @return string
     * @throws \OutOfBoundsException
     */
    public function config(string $key = '') : string
    {
        if (array_key_exists($key, $this->config)) {
            return $this->config[$key];
        }

        throw new \OutOfBoundsException("key {$key} doesnt exists in FrankyCore::\$config");
    }


    public function setConfig(string $key, string $value) : void
    {
        $this->config[ $key ] = $value;
    }


    /**
     * @return array<string>
     */
    public function getConfig() : array
    {
        return $this->config;
    }


    /**
     * @return string
     */
    public function rootPath() : string
    {
        return $this->config('rootPath');
    }


    /**
     * @return string
     */
    public function viewPath() : string
    {
        return $this->config('viewPath');
    }


    /**
     * @return string
     */
    public function assetPath() : string
    {
        return $this->config('assetPath');
    }


    /**
     * @return string
     */
    public function cssPath() : string
    {
        return $this->config('cssPath');
    }

    public function attachBehavior(string $behaviorName, CoreBehaviorInterface $behavior) : void
    {
        $this->behaviors[$behaviorName] = $behavior;
    }

    public function removeBehavior(string $behaviorName) : CoreBehaviorInterface
    {
        if (array_key_exists($behaviorName, $this->behaviors)) {
            $behavior = $this->behaviors[$behaviorName];
            unset($this->behaviors[$behaviorName]);
            return $behavior;
        }
        throw new CoreUndefinedBehaviorException("Behavior {$behaviorName} is undefined in list of custom behaviors");
    }

    public function callBehavior(string $behaviorName, array $parameters = [])
    {
        if (array_key_exists($behaviorName, $this->behaviors)) {
            $behavior = ($this->behaviors[$behaviorName]);
            return $behavior->run($parameters);
        }
        throw new CoreUndefinedBehaviorException("Behavior {$behaviorName} is undefined in list of custom behaviors");
    }
}
