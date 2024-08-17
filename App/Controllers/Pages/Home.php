<?php 

namespace App\Controllers\Pages;

use App\Utils\MainView;
use App\Models\Entity\Menssagens as EntityMsg;
use App\Models\Entity\Organization as EntityOrganization;

class Home extends Page

{
	
/**
 * retorna a home do site
 * @return srting

 */



 public static function getIndex($request,$msg = null){
	
	$status = !is_null($msg) ? Alert::getError($msg) : '';
	$content = MainView::render('Pages/page',[
      
	  'content'=>self::getModel($request)
    
	 ]);
	 

	 return parent::getPage('CodeMind - Home',$content);
}



private static function getModel($request) {
    $pagesData = self::getPagesData();
    return implode("", self::renderPages($pagesData));
}


private static function getPagesData() {
    $diretorioPaginas =  dirname(dirname(dirname(__DIR__))).'/Resources/Views/Pages/Sessoes';
    if (!is_dir($diretorioPaginas)) {
        echo("O diretório de páginas não existe: $diretorioPaginas");
        return [];
    }

    $paginas = glob($diretorioPaginas . '/*.html');
    if ($paginas === false) {
        echo("Nenhum arquivo HTML encontrado na pasta de páginas: $diretorioPaginas");
        return [];
    }

    $views = [];
    foreach ($paginas as $pagina) {
        $nomePagina = basename($pagina, '.html');
        error_log("Processando página: $nomePagina");

        $obOrganization = EntityOrganization::getSinglePage($nomePagina);
        if ($obOrganization instanceof EntityOrganization) {
            $conteudoPagina = MainView::render('Pages/Modules/' . $obOrganization->modelo, [
                'id'        => $obOrganization->id,
                'pagina'    => $obOrganization->pagina,
                'titulo'    => $obOrganization->titulo,
                'descricao' => $obOrganization->descricao,
                'imagem'    => $obOrganization->imagem
            ]);
            $views[$nomePagina] = $conteudoPagina;
            error_log("Conteúdo da página $nomePagina renderizado com sucesso");
        } else {
            error_log("Erro ao recuperar dados da página: $nomePagina");
        }
    }

    return $views;
}

private static function renderPages($pagesData) {
    $views = [];
    foreach ($pagesData as $nomePagina => $conteudoPagina) {
        $views[$nomePagina] = MainView::render('Pages/Sessoes/' . $nomePagina, [
            'content' => $conteudoPagina
        ]);
    }
    return $views;
}


    

    public static function setClient($request) {
        if (!isset($postVars['acao'])) {
            $postVars = $request->getPostVars();
            $obMsg = new EntityMsg;

            $nome = isset($postVars['Nome']) ? trim($postVars['Nome']) : '';
            $sobreNome = isset($postVars['sobreNome']) ? trim($postVars['sobreNome']) : '';
            $msg = isset($postVars['Menssagem']) ? trim($postVars['Menssagem']) : '';
            $email = isset($postVars['Email']) ? trim($postVars['Email']) : '';
            $numero = isset($postVars['Celular']) ? trim($postVars['Celular']) : '';

            $nomeArray = explode(' ', $nome);
            $nome = $nomeArray[0];
            
            $celular = preg_replace('/[\(\)\-\s]/', '', $numero);
            if (empty($nome) || empty($sobreNome) || empty($msg) || empty($email) || empty($celular)) {
                return json_encode(['status' => 'error', 'message' => 'Todos os campos são obrigatórios.']);
                
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return json_encode(['status' => 'error','message' => 'Formato de email inválido.']);
            }

            if (strlen($celular) !== 11 || !ctype_digit($celular)) {
                return json_encode(['status' => 'error', 'message' => 'Número de celular inválido.']);
            }

            #   if(!$obMsg->sendEmail($email,$nome.' '.$sobreNome,"Ola $nome".' '."$sobreNome Agradecemos o contato, em breve um de nossos atendentes entrara em contato!)")){
            #      return json_encode(['status' => 'error','message' => 'adicione um email valido e tente novamente']);
            else{
                $obMsg->nome = $nome . ' ' . $sobreNome;
                $obMsg->email = $email;
                $obMsg->menssagem = $msg;
                $obMsg->data = date('d/m/Y H:i:s');
                $obMsg->sendMsg();
                return json_encode(['status' => 'success','message'=>'Menssagem Enviada com sucesso']);
                exit;
            }

        
        }

        // Responda com uma mensagem de sucesso
        return $request->getRoute()->redirect('/');
    }
	
}


?>