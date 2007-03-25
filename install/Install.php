<?php
/**
 * Installation operations
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
class Install
{
	/**
	* This method will test connection to the database, load database schemas
	* and finally write the config file.
	*
	* Throw an exception in case of problems.
	*
	* @param Path to the USVN config file
	* @param Database host
	* @param Database user
	* @param Database password
	* @param Database name
	* @param Database table prefix (ex: usvn_)
	*/
	static public function installDb($config_file, $host, $user, $password, $database, $prefix)
	{
		//Se connecte à la DB (cherche $this->db = Zend_Db::factory('PDO_MYSQL', $params); dans le code tu verra un exemple d'utilisation)
		//Charge le fichier SQL/SVNDB.sql dans la DB (voir USVN_Db_Utils::loadFile)
		//Ecrit le fichier de conf
	}
}
