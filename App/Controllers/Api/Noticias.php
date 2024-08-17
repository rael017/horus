<?php
namespace App\Controllers\Api;

use App\Models\Entity\Post as EntityPost;
use App\Models\DataBase\Pagination;
use App\Utils\Redis;

/**
 * Class Noticias
 * Controlador para gerenciar as operações relacionadas às notícias na API.
 */
class Noticias extends Api {

    /**
     * Obtém uma imagem específica.
     *
     * @param object $request Objeto da requisição.
     * @param string $filename Nome do arquivo da imagem.
     */
    public static function getImage($request, $filename)
    {
        $filepath = dirname(dirname(dirname(__DIR__))) . '/Resources/Views/Components/images/codeBlog/' . $filename;

        if (file_exists($filepath)) {
            header('Content-Type: ' . mime_content_type($filepath));
            readfile($filepath);
            exit;
        } else {
            http_response_code(404);
            echo "File not found.";
            exit;
        }
    }

    /**
     * Obtém os itens de post com paginação.
     *
     * @param int $page Número da página.
     * @param object $obPagination Objeto de paginação, passado por referência.
     * @return array Itens de post.
     */
    private static function getPostItens($page, $qtd,  &$obPagination) {
        $itens = [];
        $qtdTotal = EntityPost::getPost(null, null, null, 'COUNT(*) as qtd')->fetchObject()->qtd;

        $obPagination = new Pagination($qtdTotal, $page, $qtd);
        $results = EntityPost::getPost(null, 'id DESC', $obPagination->getLimit());

        while ($obPost = $results->fetchObject(EntityPost::class)) {
            $imagemApi = URL . '/api/V1/imagens/' . $obPost->imagem;
            $itens[] = [
                'id'       => (int)$obPost->id,
                'categoria'=> $obPost->categoria,
                'autor'    => $obPost->autor,
                'titulo'   => $obPost->titulo,
                'post'     => $obPost->post,
                'imagem'   => $imagemApi,
                'slug'     => $obPost->slug,
                'data'     => $obPost->data
            ];
        }
        return $itens;
    }

    /**
     * Obtém os itens de post por categoria com paginação.
     *
     * @param string $category Categoria dos posts.
     * @param int $page Número da página.
     * @param int $qtd Quantidade de itens por página.
     * @param object $obPagination Objeto de paginação, passado por referência.
     * @return array Itens de post.
     */

    private static function getPostItensByCategory($category,$page,$qtd ,&$obPagination){
        $itens = [];
        $qtdTotal = EntityPost::getPost('categoria = "' . $category . '"', null, null, 'COUNT(*) as qtd')->fetchObject()->qtd;

        $obPagination = new Pagination($qtdTotal, $page, $qtd);
        $limit = $obPagination->getLimit(); // "offset, limit"
        $results = EntityPost::getPost('categoria = "' . $category . '"', 'id DESC', $limit);

        while ($obPost = $results->fetchObject(EntityPost::class)) {
            $imagemApi = URL . '/api/V1/imagens/' . $obPost->imagem;
            $itens[] = [
                'id'       => (int)$obPost->id,
                'categoria'=> $obPost->categoria,
                'autor'    => $obPost->autor,
                'titulo'   => $obPost->titulo,
                'post'     => $obPost->post,
                'imagem'   => $imagemApi,
                'slug'     => $obPost->slug,
                'data'     => $obPost->data
            ];
        }
        return $itens;
    }

    /**
     * Obtém todos os posts encontrados em uma busca.
     *
     * @param object $request Objeto da requisição.
     * @param string $arg Termo de busca.
     * @param object $obPagination Objeto de paginação, passado por referência.
     * @return array Itens de post encontrados na busca.
     */

    private static function search($request, $arg, &$obPagination) {
        $itens = [];
        $busca = strip_tags($arg);

        $condicoes = [
            strlen($busca) ? 'titulo LIKE "%' . str_replace(' ', '%', $busca) . '%"' : null
        ];

        $condicoes = array_filter($condicoes);
        $where = implode(' AND ', $condicoes);

        $qtdTotal = EntityPost::getPost($where, null, null, 'COUNT(*) as qtd')->fetchObject()->qtd;
        $queryParams = $request->getQueryParams();
        $paginaAtual = $queryParams['page'] ?? 1;

        $obPagination = new Pagination($qtdTotal, $paginaAtual, 5);
        $results = EntityPost::getPost($where, null, $obPagination->getLimit());

        while ($obPost = $results->fetchObject(EntityPost::class)) {
            $imagemApi = URL . '/api/V1/imagens/' . $obPost->imagem;
            $itens[] = [
                'id'       => (int)$obPost->id,
                'autor'    => $obPost->autor,
                'titulo'   => $obPost->titulo,
                'data'     => $obPost->data,
                'imagem'   => $imagemApi,
                'categoria'=> $obPost->categoria,
                'slug'     => $obPost->slug,
                'post'     => $obPost->post
            ];
        }
        return $itens;
    }

    /**
     * Obtém todos os posts
     *
     * @param object $request Objeto da requisição.
     * @return array Dados dos posts e informações de paginação.
     */
    public static function getPost($request)
    {
        $queryParams = $request->getQueryParams();
        $page = $queryParams['page'] ?? 1;

        $qtd = $queryParams['qtd'] ?? 10;
        $redisClient = new Redis();

        $cacheKey = 'get_post_page_'.$page.'_qtd_'.$qtd;

        // verifica se os dados estao no cache
        if($redisClient->exists($cacheKey)){
            return json_decode($redisClient->get($cacheKey), true);
        }
        $posts = self::getPostItens($page, $qtd, $pagination);

        $data = [
            'noticias' => $posts,
            'paginacao' => $pagination ? self::getPagination($request, $pagination) : null
        ];

        // Armazena os dados no cache Redis por 1 hora (3600 segundos)
        $redisClient->set($cacheKey, json_encode($data), 3600);

        return $data;
    }

    /**
     * Obtém os posts de uma categoria específica com paginação.
     *
     * @param object $request Objeto da requisição.
     * @param string $category Categoria dos posts.
     * @return array Dados dos posts e informações de paginação.
     */

    public static function getPostCategory($request,$category)
    {
        $queryParams = $request->getQueryParams();
        $page = $queryParams['page'] ?? 1;

        $qtd = $queryParams['qtd'] ?? 10;
        $redisClient = new Redis();
        $cacheKey = 'get_post_category_' . md5($category) . '_page_' . $page . '_qtd_' . $qtd;


       // Verifica se os dados estão no cache
        if ($redisClient->exists($cacheKey)) {
            return json_decode($redisClient->get($cacheKey), true);
        }

        $posts = self::getPostItensByCategory($category,$page,$qtd, $pagination);

        $data = [
            'noticias' => $posts,
            'paginacao' => $pagination ? self::getPagination($request, $pagination) : null
        ];

        // Armazena os dados no cache Redis por 1 hora (3600 segundos)
        $redisClient->set($cacheKey, json_encode($data), 3600);

        return $data;
    }

    /**
     * Obtém os posts que correspondem a uma busca com paginação.
     *
     * @param object $request Objeto da requisição.
     * @param string $arg Termo de busca.
     * @return array Dados dos posts e informações de paginação.
     */

    public static function getPostOfSearch($request, $arg) {
        $posts = self::search($request, $arg, $pagination);

        return [
            'noticias' => $posts,
            'paginacao' => $pagination ? self::getPagination($request, $pagination) : null
        ];
    }

    /**
     * Obtém um post específico pelo ID.
     *
     * @param object $request Objeto da requisição.
     * @param int $id ID do post.
     * @return array Dados do post.
     * @throws \Exception Se o post não for encontrado.
     */
    public static function getPostSingle($request, $id) {
        $redisClient = new Redis();
        $cacheKey = 'get_post_single_' . $id;

        // Verifica se os dados estão no cache
        if ($redisClient->exists($cacheKey)) {
            return json_decode($redisClient->get($cacheKey), true);
        }

        // Obtém os dados do banco de dados
        $obPost = EntityPost::getForEdit($id);

        if (!$obPost instanceof EntityPost) {
            throw new \Exception("O Post " . $id . " Não foi encontrado", 404);
        }

        $imagemApi = URL . '/api/V1/imagens/' . $obPost->imagem;

        $data = [
            'id'       => (int)$obPost->id,
            'categoria'=> $obPost->categoria,
            'autor'    => $obPost->autor,
            'titulo'   => $obPost->titulo,
            'post'     => $obPost->post,
            'imagem'   => $imagemApi,
            'slug'     => $obPost->slug,
            'data'     => $obPost->data
        ];

        // Armazena os dados no cache Redis por 1 hora (3600 segundos)
        $redisClient->set($cacheKey, json_encode($data), 3600);

        return $data;
    }


    /**
     * Atualiza um post existente.
     *
     * @param object $request Objeto da requisição.
     * @param int $id ID do post a ser atualizado.
     * @return array Dados do post atualizado.
     * @throws \Exception Se os dados obrigatórios não forem fornecidos ou se o post não for encontrado.
     */
    public static function setEditPost($request, $id) {
        $postVars = $request->getPostVars();
        if (empty($postVars['titulo']) || empty($postVars['post'])) {
            throw new \Exception('Insira o título e o post', 400);
        }

        $obPost = EntityPost::getForEdit($id);

        if (!$obPost instanceof EntityPost) {
            throw new \Exception("O Post " . $id . " Não foi encontrado", 404);
        }

        $obPost->categoria = 'Geral';
        $obPost->titulo = $postVars['titulo'];
        $obPost->post = $postVars['post'];
        $obPost->AtualizarPost();

        return [
            'id'       => (int)$obPost->id,
            'categoria'=> $obPost->categoria,
            'titulo'   => $obPost->titulo,
            'post'     => $obPost->post,
        ];
    }


        
    public static function setNewPost($request){

        $postVars = $request->getPostVars();
        $titulo = $postVars['titulo'];
        $post   = $postVars['post'];

        if(!isset($titulo) || !isset($post)){
            throw new \Exception('insira o titulo e o post',400);
            exit;
        }else if($titulo instanceof EntityPost){
            throw new \Exception('titulo ja existe',400);
            exit;
        }

        
        $obPost = new EntityPost;
        $obPost->categoria = 'Geral';
        $obPost->autor = $request->user->nome;
        $obPost->titulo = $postVars['titulo'];
        $obPost->post = $postVars['post'];
        $obPost->registerInAnalize();

        return [
                'id'       =>(int)$obPost->id,
                'categoria'=>$obPost->categoria,
                'autor'    =>$obPost->autor,
                'titulo'   =>$obPost->titulo,
                'post'     =>$obPost->post,
                'data'     =>$obPost->data
        ];
    }
}
?>