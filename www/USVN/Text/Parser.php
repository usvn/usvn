<?php
/**
 * Transform wiki syntax to HTML syntax.
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.5
 * @package text
 * @subpackage syntax
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This project has been realised as part of
 * end of studies project.
 *
 * $Id$
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
        $table = False;
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
            if (substr($line, -2) == '||') {
                $line = preg_replace('/^\|\|(.*)\|\|$/', '<tr><td>\1</td></tr>', $line);
                $line = str_replace('||', '</td><td>', $line);
                if (!$table) {
                    $line = '<table>'.$line;
                    $table = True;
                }
            }
            else {
                if ($table) {
                    $line = '</table>'.$line;
                    $table = False;
                }
            }
            $result .= $line."\n";
        }
        while ($list_level) {
            $list_level--;
            $result .= '</li>';
        }
        if ($table) {
            $result .= '</table>';
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

    static private function parseBloc($str)
    {
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
            "''" => 'i',
            '--' => 'del');
        while ($html = current($replace_tag)) {
            $tag = key($replace_tag);
            $start = "<$html>";
            $end = "</$html>";
            $str = self::findAndReplaceTags($str, $tag, $start, $end);
            next($replace_tag);
        }
        return $str;
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
        $result = "";
        $is_verbatim = False;
        foreach (explode("{{{", $str) as $bloc) {
            if (!$is_verbatim) {
                $result .= self::parseBloc($bloc);
            }
            else {
                $pos = strrpos($bloc, '}}}');
                if ($pos === false) {
                    $result .= '<pre>'.$bloc.'</pre>';
                }
                else{
                    $len = strlen($bloc);
                    $result .= '<pre>'.substr($bloc, 0, $pos).'</pre>'.substr($bloc, $pos + 3, $len - $pos - 3);
                    $is_verbatim = False;
                }
            }
            $is_verbatim = ($is_verbatim == True) ? False : True;
        }
        return $result;
    }
}
