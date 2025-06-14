<?php

namespace App\Logging;

use App\Mail\SystemErrorMail;
use Monolog\Logger;
use Monolog\Handler\AbstractProcessingHandler;
use Illuminate\Support\Facades\Mail;
use Monolog\LogRecord;

class CreateMailErrorChannel
{
    public function __invoke(array $config): Logger
    {
        $handler = new class extends AbstractProcessingHandler {
            protected function write(LogRecord $record): void
            {
                try {
                    $emailsString = env('SENDING_EMAIL', 'eyvaz.ceferov@gmail.com');
                    $emails = explode(',', $emailsString);

                    $mailable = new SystemErrorMail($record);

                    foreach ($emails as $emailAddress) {
                        $email = trim($emailAddress);
                        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                            Mail::to($email)->queue($mailable);
                        }
                    }
                } catch (\Exception $e) {
                    logger()->channel('single')->error(
                        'MAIL GÖNDƏRİLƏRKƏN XƏTA BAŞ VERDİ: ' . $e->getMessage()
                    );
                }
            }
        };

        return new Logger('mail_errors', [$handler]);
    }
}
