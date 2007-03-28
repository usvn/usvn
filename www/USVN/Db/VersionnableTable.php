<?php
/**
 * Class for manipulate versionnable table
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.5
 * @package db
 * @subpackage versionnable
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This project has been realised as part of
 * end of studies project.
 *
 * $Id$
 */
 class USVN_Db_VersionnableTable extends USVN_Db_Table {

	/**
     * Updates existing rows after
     *
     * Columns must be in underscore format.
     *
     * @param array $data Column-value pairs.
     * @param string $where An SQL WHERE clause.
     * @return int The number of rows updated.
     */
	public function update(&$data, $where)
	{
		//    	Faire la copie vers la table de sauvegarde des versions
		parent::update($data, $where);
	}

}
