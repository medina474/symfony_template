<?php

namespace App\MessageHandler;

use App\Utility\CsvImporter;
use App\Message\ImportMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler()]
final readonly class ImportMessageHandler
{
    public function __construct(
        private CsvImporter $csvImporter,
    ) {
    }

    public function __invoke(ImportMessage $message): void
    {
        $this->csvImporter->importCsv($message->content, $message->importId);
    }
}
