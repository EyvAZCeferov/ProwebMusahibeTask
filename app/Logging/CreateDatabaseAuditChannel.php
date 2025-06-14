<?php

namespace App\Logging;

use App\Models\AuditLogs;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;
use Monolog\LogRecord;

class CreateDatabaseAuditChannel
{
    public function __invoke(array $config): Logger
    {
        $handler = new class extends AbstractProcessingHandler {
            protected function write(LogRecord $record): void
            {
                if (isset($record['context']) && is_array($record['context'])) {
                    try {
                        AuditLogs::create($record['context']);
                    } catch (\Exception $e) {
                        logger()->channel('single')->error(
                            'MAIL GÖNDƏRİLƏRKƏN XƏTA BAŞ VERDİ: ' . $e->getMessage()
                        );
                    }
                }
            }
        };

        return new Logger('database_audit', [$handler]);
    }
}
