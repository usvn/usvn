<?php

/**
* 
*/
class Default_Model_MilestonesMapper
{
  static private $_instance = null;
  protected $_dbTable;

  static public function getInstance()
  {
    if (self::$_instance === null) {
      self::$_instance = new Default_Model_MilestonesMapper(new Default_Model_DbTable('usvn_milestones'));
		}
    return self::$_instance;
  }

  public function getDbTable()
  {
    return $this->_dbTable;
  }

  function __construct($dbTable = null)
  {
    $this->_dbTable = $dbTable;
    return $this;
  }

	public function save(Default_Model_Milestone $milestone)
  {
		$data = array(
			'project_id'		 		=> $milestone->getProjectId(),
			'creation_date' 		=> $milestone->getCreationDate(),
			'creator_id' 				=> $milestone->getCreatorId(),
			'modification_date' => $milestone->getModificationDate(),
			'modificator_id' 		=> $milestone->getModificatorId(),
			'title' 						=> $milestone->getTitle(),
			'description' 			=> $milestone->getDescription(),
			'due_date' 					=> $milestone->getDueDate(),
			'status'			 			=> $milestone->getStatus()
			);

		if (null === ($id = $milestone->getId())) {
			unset($data['id']);
			$this->getDbTable()->insert($data);
		} else {
			$this->getDbTable()->update($data, array('milestone_id = ?' => $id));
		}
  }

	public function delete(Default_Model_Milestone $milestone)
	{
		$id = $milestone->getId();
		$this->getDbTable()->delete(array('milestone_id = ?' => $id));
	}

  public function find($id)
  {
		$result = $this->getDbTable()->find($id);
		if (0 == count($result)) {
			return null;
		}
		return new Default_Model_Milestone($result->current());
  }

  public function fetchAll($where = null, $order = null)
  {
		$resultSet = $this->getDbTable()->fetchAll($where, $order);
		$entries   = array();
		foreach ($resultSet as $row) {
			$entries[] = new Default_Model_Milestone($row);
		}
		return $entries;
  }

	public function fetchRow($where = null, $order = null)
  {
		$row = $this->getDbTable()->fetchRow($where, $order);
		if ($row === null) {
			return null;
		}
		return new Default_Model_Milestone($row);
  }
}
