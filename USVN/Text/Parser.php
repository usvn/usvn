<?php
/**
* Transform wiki syntax to HTML syntax
*
* @author Julien Duponchelle
* @package text
* @subpackage syntax
* @since 0.1
*/

class USVN_Text_Parser
{
    /**
    * Start or end of tag
    *
    * @var bool
    */
    static private $replace_status;

    /**
    * Simple replace of '' __ ''' by html tags.
    *
    * This method is call by findAndReplaceTags for each event.
    *
    * @var string wiki tag
    * @var string start tag
    * @var string end tag
    * @return string html tag
    */
    static private function replaceTags($str, $start, $end)
    {
        if (self::$replace_status) {
            self::$replace_status = 0;
            return $start;
        }
        else {
            self::$replace_status = 1;
            return $end;
        }
    }

    /**
    * Analyse line by line to transform wiki syntax to HTML tags
    *
    * @todo noplay: Clean this method
    */
    static private function parseLineByLine($str)
    {
        $result = "";
        $list_level = 0;
        foreach(explode("\n", $str) as $line) {
            $level = strspn($line, '*');
            if ($level) {
                $line = substr($line, $level);
                $line = trim($line);
                $line = '<ul>'.$line.'</ul>';
                while ($level < $list_level) {
                    $line = '</li>'.$line;
                    $list_level--;
                }
                while ($level > $list_level) {
                    $line = '<li>'.$line;
                    $list_level++;
                }
            }
            $result .= $line."\n";
        }
        while ($list_level) {
            $list_level--;
            $result .= '</li>';
        }
        return $result;
    }

    /**
    * Search tag and replace it by corresponding start and end
    *
    * @var string text
    * @var string
    * @var string start tag
    * @var string end tag
    * @return string
    */
    private static function findAndReplaceTags($str, $tag, $start, $end)
    {
        self::$replace_status = 1;
        return preg_replace("/($tag)/e","self::replaceTags('\\1', '$start', '$end')", $str);
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
        $str = preg_replace('/===[ ]*((.*)[[:punct:][:alnum:]]+)[ ]*===\n/','<h3>\1</h3>', $str);
        $str = preg_replace('/==[ ]*((.*)[[:punct:][:alnum:]]+)[ ]*==\n/','<h2>\1</h2>', $str);
        $str = preg_replace('/=[ ]*((.*)[[:punct:][:alnum:]]+)[ ]*=\n/','<h1>\1</h1>', $str);

        $str = preg_replace('/(^|[^\[]+)(http[s]?:\/\/[^[:space:]]*)/','\1<a href="\2">\2</a>', $str);
        $str = preg_replace('/\[\[(http[s]?:\/\/[^[:space:]]*)\|([^\]]*)\]\]/','<a href="\1">\2</a>', $str);

        $str = self::parseLineByLine($str);

        $str = rtrim($str);
        $str = str_replace("\n\n", '<br /><br />', $str);
        $str = str_replace("\n", ' ', $str);
        $str = str_replace("----", '<hr />', $str);

        $str = self::findAndReplaceTags($str, "'''''", '<b><i>',  '</i></b>');

        $replace_tag = array(
            '__' => 'u',
            "'''" => 'b' ,
            "''" => 'i',);
        while ($html = current($replace_tag)) {
            $tag = key($replace_tag);
            $start = "<$html>";
            $end = "</$html>";
            $str = self::findAndReplaceTags($str, $tag, $start, $end);
            next($replace_tag);
        }
        return $str;
    }
}
?>