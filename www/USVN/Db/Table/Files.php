<?php
/**
 * Model for files table
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
 * Model for files table
 *
 * Extends USVN_Db_Table for magic configuration and methods
 *
 */
class USVN_Db_Table_Files extends USVN_Db_Table {
	/**
	 * The primary key column (underscore format).
	 *
	 * Without our prefixe...
	 *
	 * @var array
	 */
	protected $_primary = array("projects_id", "revisions_num", "files_id");

	/**
	 * The table name derived from the class name (underscore format).
	 *
	 * @var array
	 */
	protected $_name = "files";

	/**
     * Default classname for row
     *
     * @var string
     */
	protected $_rowClass = 'USVN_Db_Table_Row_Files';

	/**
     * Default classname for rowset
     *
     * @var string
     */
	protected $_rowsetClass = 'USVN_Db_Table_Rowset_Files';

	/**
	 * Function which return the last files's version
	 *
	 * @param int $files_id
	 * @param int $version
	 * @return USVN_Db_Table_Row
	 */
	public function fetchMaxVersionFiles($files_id, $version = null)
	{
		$db = $this->getAdapter();
		/* @var $db Zend_Db_Adapter_Abstract */

		$where = $db->quoteInto('files_path = ?', $this->path);
		if ($version != null) {
			$where .= $db->quoteInto(' AND revisions_num <= ?', $version);
		}

		$cols = $this->_cols;
		$this->_cols = array('max(revisions_num) as revisions_num');
		$ret = $this->fetchRow($where, null, 'files_path');
		$this->_cols = $cols;
		return $ret;
	}

	/**
	 * Search all matching files
	 *
	 * Return a Zend_Db_Table_Rowset containing all matching files for $project_id and $revisions
	 * where revisions is the actual max value
	 *
	 * @param int $project_id
	 * @param int $revisions
	 * @return Zend_Db_Table_Rowset
	 */
	public function fetchGetFiles($project_id, $revisions, $path = null)
	{
		$db = $this->getAdapter();
		/* @var $db Zend_Db_Adapter_Abstract */

		$where = $db->quoteInto('projects_id = ?', $project_id);
		$where .= $db->quoteInto(' AND revisions_num <= ?', $revisions);
		if ($path != null) {
			$where .= $db->quoteInto(' AND files_path like ?', $path);}
			$group = $db->quoteInto('GROUP BY', $path);
			return $this->fetchAll($where, null, null, null, $group);
	}


	/**
	 * Recupere les fichiers correspondants aux parametres
	 *
	 * @param unknown_type $project
	 * @param unknown_type $path
	 * @param unknown_type $version
	 */
	static public function getfiles($project, $path , $version = null)
	{

		if ($version != null) {
			/**
		 * Instancie la classe model
		 */
			$table = new USVN_Db_Table_Files();

			$files = $table->getfiles($project, $version, $path);
		}
		else {
			$table = $this->getAdapter();
			/* @var $db Zend_Db_Adapter_Abstract */

			$where = $db->quoteInto('files_path like ?', $this->path);
			$where = $db->quoteInto('projects_id = ?', $this->project);
			$cols = $this->_cols;
			$this->_cols = array('max(revisions_num) as revisions_num, files_id, files_path, files_typ_rev');
			$ret = $this->fetchRow($where, null, 'files_path');
			$this->_cols = $cols;
			return $ret;
		}


	}

	/**
	 * Recupere le fichier correspondant aux parametres
	 *
	 * @param unknown_type $project
	 * @param unknown_type $path
	 * @param unknown_type $version
	 * @return USVN_Db_TableRow_Files
	 */
	static public function getFile($project, $path, $version = null)
	{
		/**
		 * Instancie la classe model
		 */
		$table = new USVN_Db_Table_Files();

		/**
		 * Trouve le file_id exacte pour le chemin passer en parametre
		 */
		$files_id = $table->fetchRow('files_path =' . $this->path)->files_id;

		/**
		 * Trouve la derniere version du fichier inferieure ou egale a $version
		 */
		$version = $table->fetchMaxVersionFiles($this->files_id, $version)->revisions_num;

		/**
		 * Retourne un objet resultat
		 */
		return $table->find($project, $version, $files_id);
	}
}
