<?php

class USVN_modules_default_models_Files extends USVN_Db_Table {
	/**
	 * The primary key column (underscore format).
	 *
	 * Without our prefixe...
	 * 
	 * @var array
	 */
	protected $_primary = array("projects_id", "revisions_num","files_id");

	/**
	 * The table name derived from the class name (underscore format).
	 *
	 * @var array
	 */
	protected $_name = "files";

	/**
     * Function which return the last files's version
     *
     * @param int $files_id
     * @param int $version
     */
	public function fetchMaxVersionFiles($files_id, $version = null)
	{
		$db = $this->getAdapter();
		/* @var $db Zend_Db_Adapter_Abstract */

		$where = $db->quoteInto('files_path = ?', $this->path);
		if ($version != null) {
			$where .= $db->quoteInto(' AND revisions_num <= ?', $version);
		}

		Zend_Debug::dump($this->_cols);
		$cols = $this->_cols;
		$this->_cols = array('max(revisions_num) as revisions_num');
		$ret = $this->fetchRow($where, null, 'files_path');
		$this->_cols = $cols;
		return $ret;
	}


	public function fetchGetFiles($project_id, $revisions)
	{
		$db = $this->getAdapter();
		/* @var $db Zend_Db_Adapter_Abstract */

		$where = $db->quoteInto('projects_id = ?', $project_id);
		$where .= $db->quoteInto(' AND revisions_num = ?', $revisions);

		return $this->fetchAll($where);
	}
	
	static public function getInstance($project, $path, $version)
	{
		$this->path = $path;
		$this->project = $project;
		//On recherche le fichier dans la DB et on met $this->version à la version trouvé (Voir getVersion pour plus d'explication)
		//on recherche l'id du fichier en private
		$files = new USVN_modules_default_models_Files();
		
		$files = $table->fetchRow('files_path =' . $this->path);
		$this->files_id = $files->files_id;

		//$this->version = getversion();
		$table = new USVN_modules_default_models_Files();

		$result = $table->fetchMaxVersionFiles($this->files_id);

		$this->version = $result->revisions_num;
	}
}
