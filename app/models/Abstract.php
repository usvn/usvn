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

  static public function getDbTableName()
  {
    return 'usvn_' . strtolower(array_pop(explode('_', get_called_class()))) . 's';
  }

  static public function getDbTableConfig()
  {
    $config = array();
    $config[Zend_Db_Table::NAME] = self::getDbTableName();
    $config[Zend_Db_Table::PRIMARY] = array('id');
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
      self::$_db_tables[$class] = new $tableClass('', self::getDbTableConfig());
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
    for (self::getDbTable()->getColumnsNames() as $field)
      $this->_values[$field] = $theValues[$field];
  }

  protected function _initWithRow(Zend_Db_Table_Row $row)
  {
  }

  public function __set($name, $value)
  {
    $setter = 'set' . ucfirst($name);
    if (method_exists($this, $setter))
      $this->$setter($value);
    $this->_values[$field] = $value;
  }

  public function __get($name)
  {
    $getter = 'get' . ucfirst($name);
    if (method_exists($this, $getter))
      return $this->$getter();
    return $this->_values[$field];
  }
  
}
