<?php
namespace App\Controllers\Api;


use App\Models\Entity\Post as EntityPost;
use App\Models\Entity\category as EntityCategory;
use App\Models\DataBase\Crud;
use App\Models\DataBase\Pagination;

class Imagens extends Api{

        public static function getImage($request, $filename)
        {
            $filepath = dirname(dirname(dirname(__DIR__))).'/Resources/Views/Components/images/codeblog/' . $filename;

            if (file_exists($filepath)) {
                header('Content-Type: ' . mime_content_type($filepath));
                readfile($filepath);
                exit;
            } else {
                http_response_code(404);
                echo "File not found ".$filepath;
                exit;
            }
        }
    }
?>