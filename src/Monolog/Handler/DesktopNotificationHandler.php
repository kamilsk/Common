<?php

declare(strict_types = 1);

namespace OctoLab\Common\Monolog\Handler;

use Joli\JoliNotif;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
final class DesktopNotificationHandler extends AbstractProcessingHandler
{
    /** @var string */
    private $name;
    /** @var JoliNotif\Notifier|null */
    private $notifier;
    /** @var JoliNotif\Notification */
    private $notification;

    /**
     * @param string $name
     * @param int|string $level
     * @param bool $bubble
     *
     * @api
     */
    public function __construct(string $name, $level = Logger::DEBUG, bool $bubble = true)
    {
        \assert('is_int($level) || is_string($level)');
        parent::__construct($level, $bubble);
        $this->name = $name;
        $this->notifier = JoliNotif\NotifierFactory::create();
        $this->notification = new JoliNotif\Notification();
    }

    /**
     * @param array $record
     *
     * @throws \Joli\JoliNotif\Exception\InvalidNotificationException
     *
     * @api
     */
    protected function write(array $record)
    {
        \assert('array_key_exists(\'formatted\', $record)');
        $this->notification
            ->setTitle(sprintf('[%s] %s', Logger::getLevelName($this->level), $this->name))
            ->setBody($record['formatted']);
        $this->notifier && $this->notifier->send($this->notification);
    }
}
