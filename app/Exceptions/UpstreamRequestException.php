<?php

namespace App\Exceptions;

class UpstreamRequestException extends \RuntimeException
{
    /** @var array<string,mixed>|null */
    protected ?array $responsePayload;

    /**
     * @param string $message
     * @param int $statusCode HTTP-status of upstream-response
     * @param array<string,mixed>|null $responsePayload upstream response body (if exists)
     */
    public function __construct(string $message, int $statusCode, ?array $responsePayload = null)
    {
        parent::__construct($message, $statusCode);
        $this->responsePayload = $responsePayload;
    }

    public function status(): int
    {
        return $this->getCode();
    }

    public function response(): ?array
    {
        return $this->responsePayload;
    }
}
