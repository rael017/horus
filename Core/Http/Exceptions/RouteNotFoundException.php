<?php
namespace Core\Http\Exceptions;

class RouteNotFoundException extends \Exception
{
    private array $debugInfo;

    public function __construct(string $message = "Route not found.", array $debugInfo = [], int $code = 404, ?\Throwable $previous = null)
    {
        $this->debugInfo = $debugInfo;
        parent::__construct($message, $code, $previous);
    }

    public function getDebugInfo(): array
    {
        return $this->debugInfo;
    }
}