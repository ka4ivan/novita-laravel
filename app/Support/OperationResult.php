<?php

namespace App\Support;

use Ka4ivan\LaravelLogger\Facades\Llog;

class OperationResult
{
    /**
     * @param string $status
     * @param string $message
     * @param $data
     */
    public function __construct(
        private string $status,
        private string $message,
        private $data,
    ) {
        $this->logResult();
    }


    public static function success($message, $data = null)
    {
        return new self('success', $message, $data);
    }

    public static function debug($message, $data = null)
    {
        return new self('debug', $message, $data);
    }

    public static function info($message, $data = null)
    {
        return new self('info', $message, $data, []);
    }

    public static function warning($message, $data = null)
    {
        return new self('warning', $message, $data);
    }

    public static function error($message, $data = null)
    {
        return new self('error', $message, $data);
    }

    public function doThrow(): \Exception
    {
        throw new \Exception($this->message ?: '');
    }

    public function isSuccess(): bool
    {
        return $this->status === 'success';
    }

    public function isDebug(): bool
    {
        return $this->status === 'debug';
    }

    public function isInfo(): bool
    {
        return $this->status === 'info';
    }


    public function isOk(): bool
    {
        return $this->isInfo() || $this->isSuccess() || $this->isDebug();
    }

    public function isError(): bool
    {
        return $this->status === 'error';
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getData()
    {
        return $this->data;
    }

    private function logResult()
    {
        $logMessage = "Status: {$this->status}, Message: {$this->message}";
        if ($this->data) {
            $logMessage .= ", Data: " . json_encode($this->data, JSON_UNESCAPED_UNICODE);
        }

        // emergency, alert, critical, error, warning, notice, info, debug
        if ($this->isSuccess()) {
            if (config('app.debug')) {
                Llog::info($logMessage);
            }
        } elseif ($this->isDebug()) {
            if (config('app.debug')) {
                Llog::debug($logMessage);
            }
        } elseif ($this->isInfo()) {
            Llog::info($logMessage);
        } elseif ($this->isError()) {
            Llog::error($logMessage);
        } else {
            Llog::warning($logMessage);
        }
    }
}
