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
    static private $replace_start = array('__' => '<u>');
    static private $replace_end = array('__' => '</u>');

    static private function replaceTags($str)
    {
        if (self::$replace_status[$str]) {
            self::$replace_status[$str] = 0;
            return self::$replace_start[$str];
        }
        else {
            self::$replace_status[$str] = 1;
            return self::$replace_end[$str];
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
        self::$replace_status['__'] = 1;

        $str = htmlspecialchars($str, ENT_NOQUOTES);
        $str = str_replace("\n\n", '<br /><br />', $str);
        $str = str_replace("\n", ' ', $str);

        $str = preg_replace("/(__)/e","Parser::replaceTags('\\1')", $str);
        return $str;
    }
}
?>