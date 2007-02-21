<?php
/**
* Root for installation.
*
* @author Team USVN <contact@usvn.info>
* @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
* @copyright Copyright 2007, Team USVN
* @since 0.5
* @package install
*
** This software has been written in EPITECH <http://www.epitech.net>
** EPITECH is computer science school in Paris - FRANCE -
** under the direction of Flavien Astraud <http://www.epita.fr/~flav>.
** and Jerome Landrieu.
**
** Project : USVN
** Date    : $DATE$
*/

require_once('../library/gettext/gettext.inc');

$encoding = 'UTF-8';
$language = 'fr_FR';
T_setlocale(LC_MESSAGES, $language.'.'.$encoding);
if (!locale_emulation()) {
    printf(T_("Local %s is not supported by the system.")."<br>", $language);
}
putenv("LANG=$language.$encoding");
putenv("LANGUAGE=$language.$encoding");
bindtextdomain("messages", "../locale");
bind_textdomain_codeset("messages", "UTF-8");
textdomain("messages");

echo T_("Welcome to USVN");
echo"<br><br>";
echo T_("Copyright USVN");
