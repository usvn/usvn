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
		$this->db->insert("projects", $data);

		$data = array(
			"files_id" => 1,
			"files_filename" => 'test',
			"files_path" => 'trunk/',
			"files_num_rev" => 1,
			"projects_id" => 1
		);
		$this->db->insert("files", $data);

		$data = array(
			"files_id" => 2,
			"files_filename" => 'test',
			"files_path" => 'trunk/',
			"files_num_rev" => 2,
			"projects_id" => 1
		);
		$this->db->insert("files", $data);
	}

}
?>
