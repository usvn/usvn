<?php
/**
* Transform wiki syntax to HTML syntax
*
* @author Julien Duponchelle
* @package text
* @subpackage syntax
* @since 0.1
*/

class Parser
{
    static private $replace_status;
    static private $replace_tag = array('__' => 'u', "'''" => 'b' , "''" => 'i');

    static private function replaceTags($str)
    {
        if (self::$replace_status[$str]) {
            self::$replace_status[$str] = 0;
            return '<'.self::$replace_tag[$str].'>';
        }
        else {
            self::$replace_status[$str] = 1;
            return '</'.self::$replace_tag[$str].'>';
        }
    }

    /**
    * Transform wiki syntax to HTML syntax
    *
    * @var string String to transform from wiki syntax to HTML syntax
    * @return string HTML version
    */
    static public function parse($str)
    {
        $str = htmlspecialchars($str, ENT_NOQUOTES);
        $str = str_replace("\n\n", '<br /><br />', $str);
        $str = str_replace("\n", ' ', $str);

        $array = self::$replace_tag;
        while (current($array)) {
            $tag = key($array);
            self::$replace_status[$tag] = 1;
            $str = preg_replace("/($tag)/e","Parser::replaceTags('\\1')", $str);
            next($array);
        }
        return $str;
    }
}
?>