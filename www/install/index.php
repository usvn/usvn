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

define('CONFIG_FILE', "../config.ini");

set_include_path(get_include_path() .PATH_SEPARATOR ."..");
require_once 'USVN/autoload.php';
require_once 'Install.php';

USVN_Translation::initTranslation('en_US', '../locale');
if (file_exists(CONFIG_FILE)) {
	$config = new USVN_Config(CONFIG_FILE, 'general');
	if (isset($config->translation->locale)) {
		USVN_Translation::initTranslation($config->translation->locale, '../locale');
	}
}

include "views/head.html";
if (!isset($_GET['step'])) {
	$_GET['step'] = 1;
}
try {
	switch ($_GET['step']) {
		case 1:
			include "views/step1.html";
		break;

		case 2:
			include "views/step2.html";
		break;

		case 3:
			Install::installLanguage(CONFIG_FILE, $_POST['language']);
			$language = $_POST['language'];
			include "views/step3.html";
		break;

		case 4:
			include "views/step4.html";
		break;

		case 5:
			include "views/step5.html";
		break;
	}
}
catch (USVN_Exception $e) {
	echo $e->getMessage();
}
include "views/footer.html";
?>

