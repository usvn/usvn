<?php


class Default_Model_Mapper
{
  static private $_instances = array();

  protected $_basename = null;
  protected $_dbTableConfig = null;
  protected $_dbTable = null;

  public static function getMapper($name)
  {
    if (!isset($_instances[$name]))
    {
      $_instances[$name] = new Default_Model_Mapper($name);
    }
    return $_instances[$name];
  }
  
  public static function __callStatic($name, $args)
  {
    if (preg_match('/get([A-Z][a-zA-Z]*)Mapper/', $name, $match))
    {
      return self::getMapper($match[1]);
    }
    throw new Exception('Unknown static function "' . $name . '" of class "' . __CLASS__ . '"');
  }

  public function __construct($name)
  {
    $this->_basename = $name;
  }

  public function loadDbTableConfig()
  {
    $baseName = strtolower($this->_basename);
    $config = array();
    $config[Zend_Db_Table::NAME] = 'usvn_' . $baseName . 's';
    $config[Zend_Db_Table::PRIMARY] = array($baseName . '_id');
    return $config;
  }

  public final function getDbTableConfig()
  {
    if ($this->_dbTableConfig === null)
    {
      $this->_dbTableConfig = $this->loadDbTableConfig();
    }
    return $this->_dbTableConfig;
  }

  public function getDbTableClass()
  {
    return 'Default_Model_DbTable';
  }

  public function getModelClass()
  {
    return 'Default_Model_' . $this->_basename;
  }

  public function getDbTable()
  {
    if ($this->_dbTable === null)
    {
      $tableClass = $this->getDbTableClass();
      $this->_dbTable = new $tableClass($this->getDbTableConfig());
    }
    return $this->_dbTable;
  }

  public function create($row = null)
  {
    $class = $this->getModelClass();
    return new $class($row);
  }

  public function find($id)
  {
		$result = $this->getDbTable()->find($id);
		if (0 == count($result)) {
			return null;
		}
		return $this->create($result->current());
  }

  public function fetchAll($where = null, $order = null)
  {
		$resultSet = $this->getDbTable()->fetchAll($where, $order);
		$entries   = array();
		foreach ($resultSet as $row) {
			$entries[] = $this->create($row);
		}
		return $entries;
  }

	public function fetchRow($where = null, $order = null)
  {
		$row = $this->getDbTable()->fetchRow($where, $order);
		if ($row === null) {
			return null;
		}
		return $this->create($row);
  }

}
