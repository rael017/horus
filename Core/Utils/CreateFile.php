<?php
namespace Core\Utils;

class CreateFile {
    
        public static function createFile($fileName, $fileContent) {
            $filePath = '/opt/lampp/htdocs/codeMind/Resources/Views/Pages/Sessoes/' . $fileName . '.' . 'html';
    
            // Verifica se o arquivo já existe
            if (file_exists($filePath)) {
                return false;
            }
    
            // Tenta criar o arquivo e escrever o conteúdo nele
            if (file_put_contents($filePath, $fileContent) !== false) {
                // Define as permissões do arquivo (por exemplo, 0644)
                chmod($filePath, 0644);
                return true;
            } else {
                // Em caso de falha, retorna falso
                return false;
            }
        }
}


?>
