<?php 
namespace Core\Utils;


class Slug
{ 
	
    public static function generateSlug($str){
        $str = mb_strtolower($str);
        $str = preg_replace('/(á|ã|â|à)/','a',$str);
        $str = preg_replace('/(é|è|ê)/','e',$str);
        $str = preg_replace('/(î|ì|í)/','i',$str);
        $str = preg_replace('/(ô|õ|ò|ó)/','o',$str);
        $str = preg_replace('/(ü|û|ù|ú)/','u',$str);
        $str = preg_replace('/(@|~|-)/','-',$str);
        $str = preg_replace('/(!|\?|#|:|;|\/|_)/','',$str);
        $str = preg_replace('/(ç)/','c',$str);
        $str = preg_replace('/(-[-]{1,})/','-',$str);
        $str = preg_replace('/(,)/','-',$str);
        $str = preg_replace('/( )/','-',$str);
        $str = preg_replace('/(´|`|\^|~)/','-',$str);
        $str = strtolower($str);
        return $str;
    }
    
	
	
	
	
}
?>