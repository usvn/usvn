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
** Date    : $Date$
*/

set_include_path(get_include_path() .PATH_SEPARATOR ."..");
require_once 'USVN/Translation.php';

USVN_Translation::initTranslation('fr_FR', '../locale');

echo T_("Welcome to USVN");
echo"<br><br>";
echo T_("Copyright USVN");
