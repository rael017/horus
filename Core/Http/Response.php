<?php 
 namespace Core\Http;
 
 class Response
 {
     public function __construct(
         private mixed $content,
         private int $statusCode = 200,
         private array $headers = []
     ) {
         // Define um Content-Type padrão se não for especificado
         if (!isset($this->headers['Content-Type'])) {
             $this->headers['Content-Type'] = 'text/html; charset=utf-8';
         }
     }
 
     /**
      * Envia a resposta HTTP para o navegador (usado em ambientes não-Nexus).
      */
     public function send(): void
     {
         http_response_code($this->statusCode);
 
         foreach ($this->headers as $key => $value) {
             header("$key: $value");
         }
         
         if (str_contains($this->headers['Content-Type'], 'application/json')) {
             echo json_encode($this->content, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
         } else {
             echo $this->content;
         }
     }
     
     /**
      * Helper para criar uma resposta JSON.
      */
     public static function json(array $data, int $statusCode = 200): self
     {
         return new self($data, $statusCode, ['Content-Type' => 'application/json']);
     }
 
     // --- MÉTODOS GETTER ADICIONADOS ---
 
     /**
      * Retorna o código de status HTTP da resposta.
      * @return int
      */
     public function getStatusCode(): int
     {
         return $this->statusCode;
     }
 
     /**
      * Retorna o conteúdo da resposta.
      * @return mixed
      */
     public function getContent(): mixed
     {
         return $this->content;
     }
 
     /**
      * Retorna os cabeçalhos da resposta.
      * @return array
      */
     public function getHeaders(): array
     {
         return $this->headers;
     }
 }