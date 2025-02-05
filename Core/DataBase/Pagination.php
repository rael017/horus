<?php 

namespace Core\DataBase;


class Pagination
{
	
	/**
	 * Numero maximo de registros por pagina
	 * @var integer
	 */
	private $limit;
	
	/**
	 * Quantidade total de resultados na DB
	 * @var integer
	 */
	
	 private $results;
	 
	 /**
	  * Quantidade de paginas
	  * @var integer
	  */
	 private $pages;
	 
	 /**
	  * Pagina atual 
	  * @var integer
	  */
	 private $currentPage;
	 
	 /**
	  * Construtor da classe
	  * @param integer $results
	  * @param integer $currentPage
	  * @param integer $limit
	  */
	  
	  public function __construct($results, $currentPage = 1, $limit = 10)
	  {
		$this->results = $results;
		$this->limit = $limit;
		$this->currentPage = (is_numeric($currentPage) and $currentPage > 0) ? $currentPage : 1;
		$this->calc();
	  }
	  
	  private function calc()
	  {
		$this->pages = $this->results > 0 ? ceil($this->results / $this->limit) : 1;
		
		$this->currentPage = $this->currentPage <= $this->pages ? $this->currentPage : $this->pages;
	  }
	  
	  /**
	   * Metodo responsavel por retornar a clausula limit do SQL
	   * @return string
	   */
	  
	  public function getLimit()
	  {
		$offset = ($this->limit * ($this->currentPage -1));
		return $offset.','.$this->limit;
	  }
	  
	  /**
	   * Metodo responsavel por retornar as opções de paginas disponiveis
	   * @return array
	   */
	  
	   public function getPages()
	   {
		// não retorna a pagina
		if($this->pages == 1) return [];
		
		//Paginas
		
		$paginas = [];
		for($i = 1; $i <= $this->pages; $i++){
			$paginas[] = [
				'page'=>$i,
				'atual'=>$i == $this->currentPage
			];
		}

		return $paginas;
	   }
	  
	  
}


?>