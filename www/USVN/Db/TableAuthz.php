<?php
/**
 * Class for SQL table interface
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info/
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.5
 * @package USVN_Db
 * @subpackage Table
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This project has been realised as part of
 * end of studies project.
 *
 * $Id $
 */

/**
 * Class for SQL table interface.
 *
 * @category   USVN
 * @package	USVN_Db
 * @subpackage Table
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license	http://framework.zend.com/license/new-bsd	 New BSD License
 */
abstract class USVN_Db_TableAuthz extends USVN_Db_Table {
	/**
	 * Overload insert's method to generate the authz file.
	 *
	 * @param array $data
	 * @return integer the last insert ID.
	 */
	public function insert(array $data)
	{
		$res = parent::insert($data);
		USVN_Authz::generate();
		return $res;
	}

	/**
	 * Overload update's method to generate the authz file.
	 *
	 * @param array $datagetAdapter->
	 * @param string where SQL where
	 * @return integer The number of rows updated.
	 */
	public function update(array $data, $where)
	{
		$res = parent::update($data, $where);
		USVN_Authz::generate();
		return $res;
	}

	/**
	 * Overload delete's method to generate the authz file.
	 *
	 * @param  string $parentTableClassname
	 * @param  array  $primaryKey
	 * @return int    Number of affected rows
	 */
	public function delete($where)
	{
		$res = parent::delete($where);
		USVN_Authz::generate();
		return $res;
	}
}
