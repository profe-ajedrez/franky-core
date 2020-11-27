<?php declare(strict_types = 1);

namespace jotaa\core\core_classes;

use \MonologPHPMailer\PHPMailerHandler;

use \Monolog\Formatter\HtmlFormatter;
use \Monolog\Logger;
use Monolog\Processor\GitProcessor;
use Monolog\Processor\HostnameProcessor;
use \Monolog\Processor\IntrospectionProcessor;
use \Monolog\Processor\MemoryUsageProcessor;
use \Monolog\Processor\WebProcessor;

use PHPMailer\PHPMailer\PHPMailer;

class CoreMailHandlerPhpMailer
{
    const TEST_MAIL = 'metaljacobo@gmail.com';

    public function registerMailHandler($logger)
    {
        $mailer = new PHPMailer(true);
        $mailer->Host = 'localhost';
        $mailer->SMTPAuth = false;
        $mailer->Subject = 'PHPMailer GMail SMTP test';
        $mailer->addReplyTo(self::TEST_MAIL);

        $mailer->setFrom(self::TEST_MAIL, 'Logging Server');
        $mailer->addAddress(self::TEST_MAIL, 'Franky Api');

        $logger->pushProcessor(new IntrospectionProcessor);
        $logger->pushProcessor(new MemoryUsageProcessor);
        $logger->pushProcessor(new HostnameProcessor);
        $logger->pushProcessor(new GitProcessor());
        $logger->pushProcessor(new WebProcessor);

        $handler = new PHPMailerHandler($mailer);
        $handler->setFormatter(new HtmlFormatter);

        $logger->pushHandler($handler);
    }
}
