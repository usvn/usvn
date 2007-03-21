<?php
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
			"files_id" => 1,
			"files_path" => 'trunk/test',
			"projects_id" => 1
		);
		$this->db->insert("usvn_files", $data);

		$data = array(
			"files_id" => 2,
			"files_path" => 'trunk/test',
			"projects_id" => 1
		);
		$this->db->insert("usvn_files", $data);
	}

}
?>
