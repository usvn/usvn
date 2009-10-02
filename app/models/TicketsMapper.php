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

  public function fetchAll()
  {
		$resultSet = $this->getDbTable()->fetchAll();
		$entries   = array();
		foreach ($resultSet as $row) {
			$entries[] = new Default_Model_Ticket($row);
		}
		return $entries;
  }
}
