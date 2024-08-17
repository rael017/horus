<?php 

namespace App\Controllers\Admin;

use \App\Utils\MainView;
use \App\Utils\Upload;
use \App\Utils\CreateFile as File;
use \App\Models\DataBase\Pagination;
use \App\Models\Entity\Organization as EntityOrganization;

class Gerencia extends Page
{
	public static function getSite($request)
	{

		
		$content = MainView::render('Admin/Modulos/gerenciar/site/index',[
			'item'=>self::getItensEdit($request,$obPagination),
			'pagination'=>parent::getPagination($request,$obPagination)
		]);
		
		return parent::getPainel('Painel - Lenons',$content,'site');
		
	}

	 private static function getItensEdit($request,&$obPagination){
		$itens = '';
		$qtdTotal = EntityOrganization::getPage(null,null,null,'COUNT(*) as qtd')->fetchObject()->qtd;
		$queryParans = $request->getQueryParams();
		$paginaAtual = $queryParans['page'] ?? 1;
	
	
		$obPagination = new Pagination($qtdTotal,$paginaAtual,5);
		$results = EntityOrganization::getPage(null,'id DESC',$obPagination->getLimit());
	
		while($obPost = $results->fetchObject(EntityOrganization::class)){
			$descricao = strlen($obPost->descricao > 90) ? substr($obPost->descricao,0,90).'...' : $obPost->descricao;

			$itens .= MainView::render('Admin/Modulos/gerenciar/site/pages/item-edit',[
				'id'         =>$obPost->id,
				'pagina'     =>$obPost->pagina,
				'titulo'     =>$obPost->titulo,
				'descricao'  =>$descricao,
				'imagem'     =>$obPost->imagem,
				'status'	 =>$obPost->status
			]);
		}
		return $itens;
	 }


	private static function getItensAdd($request,&$obPagination){
		$itens = '';
		$qtdTotal = EntityOrganization::getPage(null,null,null,'COUNT(*) as qtd')->fetchObject()->qtd;
		$queryParans = $request->getQueryParams();
		$paginaAtual = $queryParans['page'] ?? 1;
	
	
		$obPagination = new Pagination($qtdTotal,$paginaAtual,5);
		$results = EntityOrganization::getPage(null,'id DESC',$obPagination->getLimit());

		while($obPost = $results->fetchObject(EntityOrganization::class)){
	
			$itens .= MainView::render('Admin/Modulos/gerenciar/site/pages/item-add',[
				'pagina'     =>$obPost->pagina,
				'modelo'	 =>$obPost->modelo,
				'titulo'     =>$obPost->titulo,
				'status'	 =>$obPost->status
				
			]);
		}
		return $itens;
	
	 }

	 
	public static function getNewPage($request,$msg = null){

		$status = !is_null($msg) ? Alert::getError($msg) : '';

		$content = MainView::render('Admin/Modulos/gerenciar/site/adicionar',[
			'item'=>self::getItensAdd($request,$obPagination),
			'modelos'=>self::renderizarItensHTML(),
			'status'=>$status,
			'pagination'=>parent::getPagination($request,$obPagination)

		]);
		
		
		return parent::getPainel('Painel Gerenciar Site',$content);
	}
	
	public static function setNewPage($request){
		
		$postVars = $request->getPostVars();
		$pagina    = $postVars['pagina'];
		$modelo	   = $postVars['modelo'];
		$titulo    = $postVars['titulo'];
		$descricao = $postVars['descricao'];

		if(isset($postVars['adicionar'])){

			if(empty($pagina) || $pagina == ' '){
				return self::getNewPage($request,'Insira O Nome Da Pagina');
				exit;
				
			}else if(empty($modelo) || $modelo == ' '){
				return self::getNewPage($request,'Insira O Modelo Da Pagina');
				exit;

			}else if(empty($titulo) || $titulo == ' '){
				return self::getNewPage($request,'Insira O Titulo Da Pagina');
				exit;

			}else if(empty($descricao) || $descricao == ' '){
				return self::getNewPage($request,'Insira O Titulo Da Pagina');
				exit;

			}else if(!isset($_FILES['img']) || $_FILES['img']['error'] !== UPLOAD_ERR_OK){
				return self::getNewPage($request,'Insira Uma Imagem Para A Pagina');
				exit;

			}else{
				$obOrganization = new EntityOrganization;

				$upload = new Upload($_FILES['img']);

				$dir = dirname(dirname(dirname(__DIR__))).'/Resources/Views/Components/images/codemind';

				$uploadSuccess = $upload->upload($dir);

				if($uploadSuccess) {
					
					$nomeImagem = $upload->getBasename();					
				} else {
					return  self::getNewPage($request,'Erro Ao Fazer Upload Da Imagem');
					exit; 
				}
					$obOrganization->pagina  	= $pagina;
					$obOrganization->modelo  	= $modelo;
					$obOrganization->titulo     = $titulo;
					$obOrganization->descricao  = $descricao;
					$obOrganization->imagem     = $nomeImagem;
					$obOrganization->status     = 'ativo';
					$obOrganization->setPage();
					File::createFile($pagina,'{{content}}');
					return self::getNewPage($request);
			}

			
		}
		return self::getNewPage($request);
		
	}


	public static function getEditPage($request,$id){
		$obPost = EntityOrganization::getForEdit($id);

		if(!$obPost instanceof EntityOrganization){
			$request->getRoute()->redirect('Admin/gerenciar-site');
		}
		

		$content = MainView::render('Admin/Modulos/gerenciar/site/editar',[
			
			'title'     =>'Editar Post',
			'pagina'    =>$obPost->pagina,
			'modelos'	=>self::renderizarItensHTML(),
			'modelo'	=>$obPost->modelo,
			'titulo'    =>$obPost->titulo,
			'descricao' =>$obPost->descricao,
			'imagem'    =>$obPost->imagem,
			'status'	=>$obPost->status
		]);
		
		
		
		return parent::getPainel('Painel Gerenciar Site',$content);
			
	}

	
	public static function setEditPage($request,$id){
		$obPost = EntityOrganization::getForEdit($id);

		if(!$obPost instanceof EntityOrganization){
			$request->getRoute()->redirect('/Admin/gerenciar/site');
		}
		
		$postVars = $request->getPostVars();
		if(isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
        // Instancia a classe Upload com o arquivo de imagem enviado
        $upload = new Upload($_FILES['imagem']);

        // Define o diretório de destino para o upload da imagem
        $dir = dirname(dirname(dirname(__DIR__))).'/Resources/Views/Components/images/codeMind';
		
        // Realiza o upload da imagem
        $uploadSuccess = $upload->upload($dir);

        // Verifica se o upload foi bem-sucedido
        if($uploadSuccess) {
            // Obtém o nome da imagem após o upload
            $nomeImagem = $upload->getBasename();

            // Atualiza o nome da imagem na entidade Organization
            $obPost->imagem = $nomeImagem;
        } else {
            // Exibe uma mensagem de erro caso o upload falhe
            echo "Erro ao fazer o upload da imagem.";
            exit; // Ou redirecione para outra página
        }

    }

		$obPost->pagina    = $postVars['pagina']    ?? $obPost->pagina;
		$obPost->modelo    = $postVars['modelo']    ?? $obPost->modelo;
		$obPost->titulo    = $postVars['titulo']    ?? $obPost->titulo;
		$obPost->descricao = $postVars['descricao'] ?? $obPost->descricao;
		$obPost->imagem	   = $nomeImagem            ?? $obPost->imagem;
		$obPost->status    = $postVars['status']    ?? $obPost->status;
		$obPost->Atualizar();
		$request->getRoute()->redirect('/Admin/gerenciar/site');

	}

	public static function renderizarItensHTML() {
    $itens = ''; // Inicializa uma string para armazenar os modelos renderizados

    // Diretório onde os arquivos HTML estão localizados
    $diretorio = dirname(dirname(dirname(__DIR__))) . '/Resources/Views/Pages/Modules/';

    // Obtém uma lista de arquivos HTML na pasta
    $modelos = glob($diretorio . '*.html');

    if ($modelos !== false) {
        // Itera sobre os arquivos encontrados
        foreach ($modelos as $modelo) {
            // Obtém o nome do arquivo sem a extensão
            $model = pathinfo($modelo, PATHINFO_FILENAME);

            // Renderiza o modelo e concatena na string $itens
            $itens .= MainView::render('Admin/Modulos/gerenciar/site/pages/models', [
                'model' => $model
            ]);
        }
    }

    return $itens;
	}

	public static function getArchivePage($request,$id){
		$obPost = EntityOrganization::getForEdit($id);

		if(!$obPost instanceof EntityOrganization){
			$request->getRoute()->redirect('Admin/gerenciar-site');
		}
		

		$content = MainView::render('Admin/Modulos/gerenciar/site/arquivar',[
			'title'     =>'Editar Post',
			'pagina'    =>$obPost->pagina,
			'modelos'	=>self::renderizarItensHTML(),
			'modelo'	=>$obPost->modelo,
			'titulo'    =>$obPost->titulo,
			'descricao' =>$obPost->descricao,
			'imagem'    =>$obPost->imagem,
			'status'	=>$obPost->status
		]);
		
		
		
		return parent::getPainel('Painel Gerenciar Site',$content);
			
	}

	



}