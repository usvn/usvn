<?php
/**
 * Model for configuration pages
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.5
 * @package admin
 * @subpackage config
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This project has been realised as part of
 * end of studies project.
 *
 * $Id$
 */

class USVN_modules_admin_models_Config
{
	static public function setLanguage($language)
	{
		var_dump($language);
		var_dump(USVN_Translation::listTranslation());
		if (in_array($language, USVN_Translation::listTranslation())) {
			$config = new USVN_Config(USVN_CONFIG_FILE, USVN_CONFIG_SECTION);
			$config->translation->locale  = $language;
			$config->save();
		}
	}
}
