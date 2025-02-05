<?php
namespace Core\Utils;

class Upload
{
    private $name;

    private $extension;

    private $type;

    private $tmpName;

    private $error;

    private $size;

    private $duplicate = 0;
    public function __construct($file)
    {
        $this->type = $file['type'];
        $this->tmpName = $file['tmp_name'];
        $this->error = $file['error'];
        $this->size = $file['size'];

        $info = pathinfo($file['name']);

        $this->name = $info['filename'];
        $this->extension = $info['extension'];
    }

    public function upload($dir,$overWhite = true)
    {
       if($this->error != 0) return false;

       $path = $dir.'/'.$this->getPossibleBasename($dir,$overWhite);

       return move_uploaded_file($this->tmpName,$path);
    }

    public function getBasename()
    {
        $extension = strlen($this->extension) ? '.'.$this->extension : '';

        $duplicate = $this->duplicate > 0 ? '-'.$this->duplicate : '';

        return $this->name.$duplicate.$extension;
    }

    private function getPossibleBasename($dir,$overWhite)
    {
        if($overWhite) return $this->getBasename();

        $basename = $this->getBasename();

        if(!file_exists($dir.'/'.$basename)){
            return $basename;
        }

        $this->duplicate++;

        return $this->getPossibleBasename($dir,$overWhite);
    }

}

?>