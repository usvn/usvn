<?php
/**
 * Class to manipulate internal repository
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.5
 * @package versionning
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This project has been realised as part of
 * end of studies project.
 *
 * $Id$
 */
class USVN_Versioning_Versioning extends Zend_Db_Table {

	/**
	 * Function which return the last file's version
	 *
	 * @param USVN_FileVersion $obj
	 * @param integer $Version
	 * @return integer
	 */
	protected function _Get_Version(USVN_FileVersion $obj = null, $Version = null)
	{
		$db = $this->getAdapter();
		$select = $db->select();
		$select->from('PROJECT_MANAGEMENT', 'max(num_rev) as MAX');

		if ($obj !== null) {
			$select->where('Filename = ?', $obj->getPath());

			if ($Version != null) {
				$select->where('num_rev <= ?', $Version);
				$select->group('Filename');
			}
		}
		/*
		** Definir comment recuperer la cle du projet
		*/
		//$select->where('project_id = ?', $project_id);

		####
		//		$select2 = ;
		//		$select2->where("truc = " . $select);
		$result = $db->fetchAll($select);
		return $result;
	}

	/**
	 * Function which makes a commit
	 *
	 * @param USVN_FileVersion $Array
	 *
	 * @param char $typ_rev which be C as commit, D as Delete
	 */

	public function commit(USVN_FileVersion $Array, $typ_rev)
	{
		$version = $this->_Get_Version() + 1;
		for($i = 0; $Array[$i] ; $i++) {
			//a definir la syntax destination du path
			if(copy($Array[$i]->getPath() /*,destination*/)) {
				$data = array(
				'date' => date(DATE_ISO8601),
				'num_rev'  => $version,
				'typ_rev' => $typ_rev,
				'property' => '',
				'message'  =>'',
				);
				$id = $table->insert($data);
			}

		}

	}

	public function update($id)
	{
		//$project_management = $table->fetchRow(array('num_rev = ?' => $version));*/
		$project = new USVN_Project_Management();
		$db_project = $table->getAdapter();

		//juste recuperer le nom du fichier et faire un like
		$where = $db_project->quoteInto('project_id like ?', $id);

		$rowset = $db_project->fetchAll();
		foreach ($rowset as $row) {
			$version = $this->_Get_Version($row->Filename);
			$table = new USVN_Project_Management();
			$db = $table->getAdapter();

			//juste recuperer le nom du fichier et faire un like
			$where = $db->quoteInto('Filename like ?', basename($Array[$i]->getPath()))
			. $db->quoteInto('AND num_rev = ?', $version);
		}
	}

	/**
	 * Function whick delete on local the file
	 * and update the file in the database as 'D'
	 *
	 * @param $Array USVN_FileVersion
	 */
	public function delete ($Array)
	{
		commit($Array, 'D');
	}
}
