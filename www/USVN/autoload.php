<?php
/**
 * Setup class auto loading.
 * Now if try new USVN_tutu() it will load require file USVN/tutu.php
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.5
 * @package usvn
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This project has been realised as part of
 * end of studies project.
 *
 * $Id$
 */

set_include_path(dirname(__FILE__) . '/../' . PATH_SEPARATOR . get_include_path());

require_once 'Zend/Loader.php';
spl_autoload_register(array('Zend_Loader', 'autoload'));

function T_($str)
{
	return USVN_Translation::_($str);
}
