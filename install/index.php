<?php
/**
 * Root for installation
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.5
 * @package install
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This project has been realised as part of
 * end of studies project.
 *
 * $Id$
 */

set_include_path(get_include_path() .PATH_SEPARATOR ."..");
require_once 'USVN/Translation.php';

USVN_Translation::initTranslation('fr_FR', '../locale');

echo T_("Welcome to USVN");
echo"<br><br>";
echo T_("Copyright USVN");
