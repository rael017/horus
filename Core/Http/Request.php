<?php
namespace Core\Http;

class Request
{
    public readonly string $uri;
    public readonly string $httpMethod;
    public readonly array $queryParams;
    public readonly array $postVars;
    public readonly array $headers;
    public readonly string $ipAddress;

    public function __construct()
    {
        $this->httpMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $this->uri = explode('?', $_SERVER['REQUEST_URI'] ?? '/')[0];
        $this->queryParams = $_GET ?? [];
        $this->headers = function_exists('getallheaders') ? getallheaders() : [];
        $this->ipAddress = $this->resolveIpAddress();
        
        // Trata corpos de requisição JSON para PUT/POST
        $this->postVars = $_POST ?? [];
        if (in_array($this->httpMethod, ['POST', 'PUT', 'PATCH']) && empty($_POST)) {
            $inputRaw = file_get_contents('php://input');
            $jsonData = json_decode($inputRaw, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $this->postVars = $jsonData;
            }
        }
    }

    private function resolveIpAddress(): string
    {
        return $_SERVER['HTTP_CLIENT_IP']
            ?? $_SERVER['HTTP_X_FORWARDED_FOR']
            ?? $_SERVER['REMOTE_ADDR']
            ?? '127.0.0.1';
    }
}
?>