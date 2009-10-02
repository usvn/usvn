<?php

/**
* 
*/
class Default_Model_TicketsMapper
{
  static private $_instance = null;
  protected $_dbTable;

  static public function getInstance()
  {
    if (self::$_instance === null) {
      self::$_instance = new Default_Model_TicketsMapper(new Default_Model_DbTable('tickets'));
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

	public function save(Default_Model_Ticket $ticket)
  {
		$data = array(
			'creation_date' 		=> $ticket->getCreationDate(),
			'creator_id' 				=> $ticket->getCreatorId(),
			'modification_date' => $ticket->getModificationDate(),
			'modificator_id' 		=> $ticket->getModificatorId(),
			'title' 						=> $ticket->getTitle(),
			'description' 			=> $ticket->getDescription()
			);

		if (null === ($id = $ticket->getId())) {
			unset($data['id']);
			$this->getDbTable()->insert($data);
		} else {
			$this->getDbTable()->update($data, array('ticket_id = ?' => $id));
		}
  }

	public function delete(Default_Model_Ticket $ticket)
	{
		$id = $ticket->getId();
		$this->getDbTable()->delete(array('ticket_id = ?' => $id));
	}

  public function find($id)
  {
		$result = $this->getDbTable()->find($id);
		if (0 == count($result)) {
			return null;
		}
		return new Default_Model_Ticket($result->current());
  }

  public function fetchAll($where = null, $order = null)
  {
		$resultSet = $this->getDbTable()->fetchAll($where, $order);
		$entries   = array();
		foreach ($resultSet as $row) {
			$entries[] = new Default_Model_Ticket($row);
		}
		return $entries;
  }

	public function fetchRow($where = null, $order = null)
  {
		$row = $this->getDbTable()->fetchRow($where, $order);
		if ($row === null) {
			return null;
		}
		return new Default_Model_Ticket($row);
  }
}
