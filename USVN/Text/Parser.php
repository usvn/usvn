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

        $str = preg_replace("/__([^_]*)__/", '<u>\1</u>', $str);
        return $str;
    }
}
?>