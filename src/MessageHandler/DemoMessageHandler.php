<?php

namespace App\MessageHandler;

use App\Message\DemoMessage;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class DemoMessageHandler
{
    public function __construct(
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(DemoMessage $message): void
    {
        $this->logger->error($message->getContent());
    }
}
