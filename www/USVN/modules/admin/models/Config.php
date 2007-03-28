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
	/**
	* Set default language in the config file
	*
	* @param string The default language
	* @throw USVN_Exception
	*/
	static public function setLanguage($language)
	{
		if (in_array($language, USVN_Translation::listTranslation())) {
			$config = new USVN_Config(USVN_CONFIG_FILE, USVN_CONFIG_SECTION);
			$config->translation->locale  = $language;
			$config->save();
		}
		else {
			throw new USVN_Exception(T_("Invalid language"));
		}
	}
}
