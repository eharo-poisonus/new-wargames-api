<?php

namespace App\Shared\Infrastructure\Logging;

use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Level;
use Monolog\LogRecord;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Throwable;

final class LokiHandler extends AbstractProcessingHandler
{
    private const string PUSH_PATH = '/loki/api/v1/push';
    private const float TIMEOUT_SECONDS = 2.0;

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly string $lokiUrl,
        private readonly string $lokiUsername,
        private readonly string $lokiPassword,
        private readonly array $defaultLabels,
        int|string|Level $level = Level::Warning,
        bool $bubble = true
    ) {
        parent::__construct($level, $bubble);
    }

    protected function write(LogRecord $record): void
    {
        if ($this->lokiUrl === '') {
            return;
        }

        try {
            $response = $this->httpClient->request('POST', rtrim($this->lokiUrl, '/') . self::PUSH_PATH, [
                'timeout' => self::TIMEOUT_SECONDS,
                'auth_basic' => $this->lokiUsername !== '' ? [$this->lokiUsername, $this->lokiPassword] : null,
                'json' => $this->payloadFor($record)
            ]);
            $response->getStatusCode();
        } catch (Throwable) {
        }
    }

    private function payloadFor(LogRecord $record): array
    {
        $labels = array_merge($this->defaultLabels, [
            'level' => strtolower($record->level->getName()),
            'channel' => $record->channel
        ]);

        if (isset($record->context['error_code'])) {
            $labels['error_code'] = (string) $record->context['error_code'];
        }

        if (isset($record->context['event'])) {
            $labels['event'] = (string) $record->context['event'];
        }

        return [
            'streams' => [
                [
                    'stream' => $labels,
                    'values' => [
                        [$this->nanosecondTimestamp($record), $this->lineFor($record)]
                    ]
                ]
            ]
        ];
    }

    private function nanosecondTimestamp(LogRecord $record): string
    {
        return $record->datetime->format('Uu') . '000';
    }

    private function lineFor(LogRecord $record): string
    {
        return json_encode([
            'message' => $record->message,
            'context' => array_diff_key($record->context, ['exception' => true]),
            'extra' => $record->extra
        ], JSON_PARTIAL_OUTPUT_ON_ERROR);
    }
}
