<?php

require_once 'USVN/Db/VersionnableTable.php';

class VersionnableTable extends USVN_Db_Table {

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
