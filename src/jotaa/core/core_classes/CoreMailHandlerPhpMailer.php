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
    public function registerMailHandler($logger)
    {
        $mailer = new PHPMailer(true);
        //$mailer->isSMTP();
        $mailer->Host = 'localhost';
        //$mailer->Port = 587;
        $mailer->SMTPAuth = false;
        //$mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        //$mailer->Username = 'metaljacobo@gmail.com';
        //$mailer->Password = base64_decode('ak1hQm9wdXMxOTgw');
        $mailer->Subject = 'PHPMailer GMail SMTP test';
        $mailer->addReplyTo('metaljacobo@gmail.com');

        $mailer->setFrom('metaljacobo@gmail.com', 'Logging Server');
        $mailer->addAddress('metaljacobo@gmail.com', 'Franky Api');

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
