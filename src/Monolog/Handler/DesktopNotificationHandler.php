<?php

declare(strict_types = 1);

namespace OctoLab\Common\Monolog\Handler;

use Joli\JoliNotif;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
class DesktopNotificationHandler extends AbstractProcessingHandler
{
    /** @var string */
    private $name;
    /** @var JoliNotif\Notifier|null */
    private $notifier;
    /** @var JoliNotif\Notification */
    private $notification;

    /**
     * @param string $name
     * @param int $level
     * @param bool $bubble
     */
    public function __construct(string $name, int $level = Logger::DEBUG, bool $bubble = true)
    {
        parent::__construct($level, $bubble);
        $this->name = $name;
        $this->notifier = JoliNotif\NotifierFactory::create();
        $this->notification = new JoliNotif\Notification();
    }

    /**
     * @param array $record
     *
     * @throws \Joli\JoliNotif\Exception\InvalidNotificationException
     */
    protected function write(array $record)
    {
        $this->notification
            ->setTitle(sprintf('[%s] %s', Logger::getLevelName($this->level), $this->name))
            ->setBody(addslashes($record['formatted']))
        ;
        $this->notifier && $this->notifier->send($this->notification);
    }
}
