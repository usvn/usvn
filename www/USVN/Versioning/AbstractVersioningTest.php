<?php
/**
 * Base class for versionning test
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.5
 * @package versionning
 * @subpackage test
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This project has been realised as part of
 * end of studies project.
 *
 * $Id$
 */
require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once 'www/USVN/autoload.php';

 abstract class USVN_Versioning_AbstractVersioningTest extends USVN_Test_DB {
	public function setUp()
	{
		parent::setUp();
		$data = array(
			"projects_id" => 1,
			"projects_name" => 'Projet Love'
		);
		$this->db->insert("usvn_projects", $data);

		$data = array(
			"users_id" => 1,
			"users_login" => "test",
		);
		$this->db->insert("usvn_users", $data);

		$data = array(
			"users_id" => 2,
			"users_login" => "root",
		);
		$this->db->insert("usvn_users", $data);

		//Revision 1
		$data = array(
			"projects_id" => 1,
			"revisions_num" => 1,
			"revisions_message" => "First commit",
			"users_id" => 1
		);
		$this->db->insert("usvn_revisions", $data);

		$data = array(
			"files_id" => 0,
			"files_path" => 'trunk',
			"files_isdir" => true,
			"revisions_num" => 1,
			"projects_id" => 1,
			"files_typ_rev" => "A"
		);
		$this->db->insert("usvn_files", $data);

		$data = array(
			"files_id" => 1,
			"files_path" => 'trunk/test',
			"revisions_num" => 1,
			"projects_id" => 1,
			"files_typ_rev" => "A"
		);
		$this->db->insert("usvn_files", $data);

		$data = array(
			"files_id" => 2,
			"files_path" => 'trunk/test2',
			"revisions_num" => 1,
			"projects_id" => 1,
			"files_typ_rev" => "A"
		);
		$this->db->insert("usvn_files", $data);

		$data = array(
			"files_id" => 1,
			"projects_id" => 1,
			"properties_name" => 'eof',
			"properties_value" => "\n"
		);

		
		//Revision 2
		$data = array(
			"projects_id" => 1,
			"revisions_num" => 2,
			"revisions_message" => "Second commit",
			"users_id" => 2
		);
		$this->db->insert("usvn_revisions", $data);

		$data = array(
			"files_id" => 3,
			"files_path" => 'trunk/test',
			"revisions_num" => 2,
			"projects_id" => 1,
			"files_typ_rev" => "M"
		);
		$this->db->insert("usvn_files", $data);

		$data = array(
			"files_id" => 3,
			"revisions_num" => 2,
			"projects_id" => 1,
			"properties_name" => 'mime',
			"properties_value" => 'jpg'
		);

		/*$data = array(
			"files_id" => 3,
			"projects_id" => 1,
			"properties_name" => 'eof',
			"properties_value" => "\r\n"
		);*/
		$this->db->insert("usvn_properties", $data);

		
		// bug de phing test sur l'insertrion precedente
		
		
		//Second Project
		$data = array(
			"projects_id" => 2,
			"projects_name" => 'Projet FENEC'
		);
		$this->db->insert("usvn_projects", $data);

		//Revision 1
		$data = array(
			"projects_id" => 2,
			"revisions_num" => 1,
			"revisions_message" => "Commit to another project",
			"users_id" => 1
		);
		$this->db->insert("usvn_revisions", $data);

		$data = array(
			"files_id" => 4,
			"revisions_num" => 2,
			"files_path" => 'trunk',
			"files_isdir" => true,
			"projects_id" => 1,
			"files_typ_rev" => "A"
		);
		$this->db->insert("usvn_files", $data);

		$data = array(
			"files_id" => 5,
			"files_path" => 'trunk/test',
			"revisions_num" => 2,
			"projects_id" => 1,
			"files_typ_rev" => "A"
		);
		$this->db->insert("usvn_files", $data);
		
	}
}
?>
