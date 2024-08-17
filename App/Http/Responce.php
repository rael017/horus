<?php 
 namespace App\Http;
 
 class Responce
 {
	 /**
	  * Codigo Da Request
	  *@var integer
	  */
	  
	  private $httpCode = 200;
	  
	  /**
	   * Cabeçalho Do Responce
	   * @var array
	   */
	  
	  private $header = [];
	  
	  
	  /**
	   * Tipo Do conteudo De Retorno
	   * @var string
	   */
	  
	  private $contentType = 'text/html';
	  
	  /**
	   * Conteudo Da Responce
	   * @var mixed
	   */
	  private $content;
	  
	  /** 
	   * Metodo Responsavel Por Iniciar A Classe E Definir Os Valores
	   * @param integer $httpCode
	   * @param mixed $content
	   * @param string $contentType
	   */
	  
	  function __construct($httpCode,$content,$contentType = 'text/html')
	   {
		  $this->httpCode = $httpCode;
		  $this->content = $content;
		  $this->setCType($contentType);
	   }
	   
	   /**
		* Responsavel Por Alterar O Content-Type do Response
		*@param string $contetType
	    */
	  
	  public function setCType($contentType)
	   {
		  $this->contentType = $contentType;
		  $this->addHeaders('Content-Type',$contentType);
	   }
	   /**
		* Metodo Responsavel Por Adcionar Um registro No Cabeçalho De Response
		*@param string $key
		*@param string $value
	    */
	   
	   public function addHeaders($key,$values)
	   {
		   $this->header[$key] = $values;
	   }
	   
	   private function sendHeaders()
	   {
		   http_response_code($this->httpCode);
		   
		   foreach($this->header as $key => $value){
			   header($key.': '. $value);
		   }
	   }
	   
	   public function sendResponse()
	   {
		   $this->sendHeaders();
		   switch ($this->contentType){
			   case 'text/html':
			   		echo $this->content;
					exit;
			   case 'application/json':
					echo json_encode($this->content, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
					exit;
				
		   	}
	   }
	   
	 
 }
?>