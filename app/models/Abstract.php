<?php

/**
* 
*/
class Default_Model_Abstract
{
  static private $_db_tables = array();
  protected $_values = array();

  static public function getDbTableColumns()
  {
    return array();
    // return array_push(parent::getFields(), 'id', 'title', ...);
  }

  static public function getDbTableConfig()
  {
    $tmp = explode('_', get_called_class());
    $baseName = strtolower(array_pop($tmp));
    $config = array();
    $config[Zend_Db_Table::NAME] = 'usvn_' . $baseName . 's';
    $config[Zend_Db_Table::PRIMARY] = array($baseName . '_id');
    return $config;
  }

  static public function getDbTableClass()
  {
    return 'Default_Model_DbTable';
  }

  final static public function getDbTable()
  {
    $class = get_called_class();
    if (!array_key_exists($class, self::$_db_tables))
    {
      $tableClass = self::getDbTableClass();
      self::$_db_tables[$class] = new $tableClass(self::getDbTableConfig());
    }
    return self::$_db_tables[$class];
  }

  public function __construct($row = null)
  {
    if ($row instanceof Zend_Db_Table_Row)
      $this->_initWithRow($row);
    elseif ($row !== null)
      $this->_initNew($row);
  }

  protected function _initNew(array $theValues)
  {
    foreach ($theValues as $name => $value)
      $this->_values[$field] = $value;
  }

  protected function _initWithRow(Zend_Db_Table_Row $row)
  {
    foreach ($row->toArray() as $name => $value)
      $this->_values[$field] = $value;
  }

  public function __isset($name)
  {
    return isset($_values[$name]);
  }

  public function __set($name, $value)
  {
    if ($name[0] != '_') {
      $setter = 'set' . ucfirst($name);
    }
    else {
      $name = substr($name, 1);
      $setter = '_set' . ucfirst($name);
    }
    if (method_exists($this, $setter))
      $this->$setter($value);
    $this->_values[$field] = $value;
  }

  public function __get($name)
  {
    if ($name[0] != '_') {
      $getter = 'get' . ucfirst($name);
    }
    else {
      $name = substr($name, 1);
      $getter = '_get' . ucfirst($name);
    }
    if (method_exists($this, $getter))
      return $this->$getter();
    return $this->_values[$field];
  }
  
}
