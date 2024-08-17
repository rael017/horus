<?php

namespace App\Models\Entity;

use \App\Models\DataBase\Crud;
class home
{
	public static $id;
	public $ip;
	public $dia;
	
    public static function clearCookies() {
        if (!empty($_COOKIE)) {
            foreach ($_COOKIE as $key => $value) {
                setcookie($key, "", time() - 3600, "/");
            }
        }
    }

    public static function contador() {
        if (isset($_COOKIE['visitedPagina'])) {
            $visitedPages = json_decode($_COOKIE['visitedPagina'], true);
    
            $currentPage = str_replace("/noticias/", "", $_SERVER['REQUEST_URI']);
            $currentPage = explode("/", $currentPage)[2];
            
            if (!isset($visitedPages[$currentPage])) {
                $visitedPages[$currentPage] = 0;
            }
            $visitedPages[$currentPage]++;
    
            setcookie('visitedPagina', json_encode($visitedPages), time() + (86400 * 30), "/");
        } else {
            $currentPage = str_replace("/noticias/", "", $_SERVER['REQUEST_URI']);
            $currentPage = explode("/", $currentPage)[2];
    
            $visitedPages = array($currentPage => 1);
            setcookie('visitedPagina', json_encode($visitedPages), time() + (86400 * 30), "/");
        }
    
        arsort($visitedPages);
    
        foreach ($visitedPages as $page => $visits) {
            $visitExists = Crud::Ready('tb_blog_visitas', 'pagina = "'.$page.'"');
            if ($visitExists->rowCount() != 0) {
                $lastVisit = Crud::Ready('tb_blog_visitas', 'pagina = "'.$page.'"')->fetchAll()[0]['dia'];
                
                if (time() - strtotime($lastVisit) >= 86400) {
                    Crud::Update('tb_blog_visitas', 'pagina = "'.$page.'"', [
                        'visitas' => $visits,
                        'dia' => date('y/m/d')
                    ]);
                }
            } else {
                self::$id = Crud::Create('tb_blog_visitas', [
                    'dia' => date('y/m/d'),
                    'pagina' => $page,
                    'visitas' => $visits
                ]);
            }
        }
    }
    

    

}

?>