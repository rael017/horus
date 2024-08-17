<?php
namespace App\Http;

class Request
{
    /**
     * Retorna o Método HTTP da requisição
     * @var array
     */
    private $httpMethod;

    /**
     * URI da página
     * @var string
     */
    private $uri;

    /**
     * Parâmetros da URL ($_GET)
     * @var array
     */
    private $queryParams = [];

    /**
     * Variáveis Recebidas ($_POST)
     * @var array
     */
    private $postVars = [];

    /**
     * Cabeçalho da Request
     * @var array
     */
    private $header = [];

    /**
     * Variáveis de Arquivo ($_FILES)
     * @var array
     */
    private $fileVars = [];

    /**
     * Instância do Router
     * @var object
     */
    private $router;

    /**
     * Endereço IP do Cliente
     * @var string
     */
    private $ipAddress;

	private $user;

    public function __construct($router)
    {
        $this->router = $router;
        $this->queryParams = $_GET ?? [];
        $this->postVars = $_POST ?? [];
        $this->header = getallheaders();
        $this->fileVars = $_FILES ?? [];
        $this->httpMethod = $_SERVER['REQUEST_METHOD'] ?? '';
        $this->uri = $_SERVER['REQUEST_URI'] ?? '';
        $this->ipAddress = $this->getIpAddress(); // Definir o endereço IP
        $this->setUri();
        $this->setPostVars();
    }

    private function setPostVars()
    {
        if ($this->httpMethod == 'GET') return false;

        $this->postVars = $_POST ?? [];

        $inputRaw = file_get_contents('php://input');

        $this->postVars = (strlen($inputRaw) && empty($_POST)) ? json_decode($inputRaw, true) : $this->postVars;
    }

    private function setUri()
    {
        $this->uri = $_SERVER['REQUEST_URI'] ?? '';
        $xUri = explode('?', $this->uri);

        $this->uri = $xUri[0];
    }

    public function getMethodHttp()
    {
        return $this->httpMethod;
    }

    public function getUri()
    {
        return $this->uri;
    }

    public function getHeaders()
    {
        return $this->header;
    }

    public function getFilesVars()
    {
        return $this->fileVars;
    }

    public function getQueryParams()
    {
        return $this->queryParams;
    }

    public function getPostVars()
    {
        return $this->postVars;
    }

    public function getRoute()
    {
        return $this->router;
    }

    /**
     * Método para obter o endereço IP do cliente
     * @return string
     */
    private function getIpAddress()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            // IP fornecido pelo ISP do usuário
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            // IP passado pelo cabeçalho do proxy
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            // IP remoto padrão
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        // Tratar o caso de múltiplos IPs em HTTP_X_FORWARDED_FOR (pegar o primeiro IP)
        if (strpos($ip, ',') !== false) {
            $ip = explode(',', $ip)[0];
        }

        return $ip;
    }

    /**
     * Método para obter o endereço IP do cliente armazenado
     * @return string
     */
    public function getIpAddressStored()
    {
        return $this->ipAddress;
    }
}
?>
