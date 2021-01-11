<?php declare(strict_types = 1);

namespace jotaa\core\core_classes;

use \Monolog\Processor\WebProcessor;

use \Monolog\Processor\MemoryUsageProcessor;
use \Monolog\Processor\IntrospectionProcessor;
use \Monolog\Logger;
use \Monolog\Formatter\HtmlFormatter;
use \MonologPHPMailer\PHPMailerHandler;
use PHPMailer\PHPMailer\PHPMailer;
use Monolog\Processor\HostnameProcessor;

use Monolog\Processor\GitProcessor;

class CoreMailHandlerPhpMailer
{
    const TEST_MAIL = 'franky_mailer@jotaa.cl';
    const DEFAULT_SUBJECT = 'Exception triggered';

    public function registerMailHandler($logger, string $subject = self::DEFAULT_SUBJECT)
    {
        $mailer = new PHPMailer(true);
        $mailer->Host = 'localhost';
        $mailer->SMTPAuth = false;
        $mailer->Subject = $subject;
        $mailer->addReplyTo(self::TEST_MAIL);

        $mailer->setFrom(self::TEST_MAIL, 'Franky Mailer');
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
