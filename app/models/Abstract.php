<?php

/**
* 
*/
class Default_Model_Abstract
{
  const SQL_DATE_FORMAT = Zend_Date::W3C;

  static private $_db_tables = array();
  private $_values = array();
  private $_relations = array();
  private $_inputErrors = null;

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

  public function getInputErrors()
  {
    return $this->_inputErrors;
  }

  public function addErrorForCol($name, $error)
  {
    $name = $this->accToCol($name);
    if (!isset($this->_inputErrors[$name]))
      $this->_inputErrors[$name] = $error;
  }

  public function validateAllCols()
  {
    $cols = self::getDbTable()->info(Zend_Db_Table::COLS);
    foreach ($cols as $col)
    {
      if (!empty($this->_inputErrors[$col]))
        continue;
      $validateMethod = 'validate' . ucfirst($this->colToAcc($col));
      if (!method_exists($this, $validateMethod))
        continue;
      $err = $this->$validateMethod();
      if (!empty($err))
        $this->addErrorForCol($col, $err);
    }
    return empty($this->_inputErrors);
  }

  public function save()
  {
    if ($this->validateAllCols() == false)
      return false;
    $values = $this->toArray();
    $t = $this->getDbTable();
    $pKey = $t->info(Zend_Db_Table::PRIMARY);
    if ($this->isNew())
    {
    	$key = $t->insert($values);
    	if (is_array($key))
        foreach ($pKey as $k)
      	  $this->_values[$k] = $key[$k];
  	  else
  	    $this->_values[$pKey[1]] = $key;
    	return true;
    }
    else
    {
      $where = $this->where();
      foreach ($pKey as $k)
        unset($values[$k]);
    	return $t->update($values, $where);
    }
  }

  public function where()
  {
    $pKey = $this->getDbTable()->info(Zend_Db_Table::PRIMARY);
    $where = array();
    foreach ($pKey as $k)
      $where["{$k} = ?"] = $this->_values[$k];
    return $where;
  }

  public function getId()
  {
    $pKey = $this->getDbTable()->info(Zend_Db_Table::PRIMARY);
    if (count($pKey) == 1)
      return (isset($this->_values[$pKey[1]]) ? $this->_values[$pKey[1]] : null);
    $id = array();
    foreach ($pKey as $ik => $k)
    {
      $id[$ik] = $this->_values[$k];
    }
    return (empty($id) == 0 ? null : $id);
  }

  public function isNew()
  {
    return ($this->getId() === null);
  }

  public function toArray()
  {
    $values = array();
    foreach ($this->_values as $key => $value)
    {
      if ($value instanceof Zend_Date)
        $value = $value->toString(self::SQL_DATE_FORMAT);
      $values[$key] = $value;
    }
    return $values;
  }

  public function setValues($values)
  {
    foreach ($values as $name => $value)
    {
      $this->__set($this->colToAcc($name), $value);
    }
  }

  public function updateWithValues($values)
  {
    $this->setValues($values);
  }

  static public function create($row = null)
  {
    $class = get_called_class();
    return new $class($row);
  }

  public function __construct($row = null)
  {
    if ($row instanceof Zend_Db_Table_Row)
      $this->_initWithRow($row);
    elseif ($row !== null)
      $this->_initNew($row);
  }

  protected function _initNew(array $values)
  {
    $this->setValues($values);
  }

  protected function _initWithRow(Zend_Db_Table_Row $row)
  {
    $metadata = $this->getDbTable()->info(Zend_Db_Table::METADATA);
    $row = $row->toArray();
    foreach ($metadata as $col => $desc)
    {
      if (array_key_exists($col, $row))
      {
        $value = $row[$col];
        switch ($desc['DATA_TYPE'])
        {
          case 'date':
          case 'datetime':
            $this->_values[$col] = (empty($value) ? null : new Zend_Date($value, self::SQL_DATE_FORMAT));
            break;
          
          default:
            $this->_values[$col] = $value;
            break;
        }
      }
    }
  }
  
  public function accToCol($name)
  {
    if ($name[0] == '_')
      $name = substr($name, 1);
    preg_match_all('!^[^A-Z]+|[A-Z][^A-Z]*!', $name, &$match);
    foreach ($match[0] as &$value)
      $value = strtolower($value);
    $name = join($match[0], '_');
    return $name;
  }

  public function colToAcc($name)
  {
    if ($name[0] == '_')
      $name = substr($name, 1);
    $tab = explode('_', $name);
    $name = array_shift($tab);
    foreach($tab as $n)
      $name .= ucfirst($n);
    return $name;
  }

  public function __isset($name)
  {
    if ($name[0] == '_')
      $name = substr($name, 1);
    $name = $this->accToCol($name);
    return isset($this->_values[$name]);
  }

  public function __set($name, $value)
  {
    USVNLog('set '.$name.' => '.$value);
    if ($name[0] != '_') {
      $setter = 'set' . ucfirst($name);
    }
    else {
      $name = substr($name, 1);
      $setter = '_set' . ucfirst($name);
    }
    $col = $this->accToCol($name);
    if (method_exists($this, $setter))
    {
      USVNLog('  call ' . $setter);
      $this->$setter($value);
    }
    else
    {
      if (!in_array($col, self::getDbTable()->info(Zend_Db_Table::COLS)))
        throw new Exception(sprintf("Unknown column '%s' in table '%s'", $col, self::getDbTable()->info(Zend_Db_Table::NAME)));
      USVNLog('  direct set ' . $col);
      $this->_values[$col] = $value;
    }
    USVNLog('  => ' . (isset($this->_values[$col]) ? '"' . $this->_values[$col] . "'" : '(null)'));
  }

  public function __get($name)
  {
    USVNLog('get '.$name);
    if ($name[0] != '_') {
      $getter = 'get' . ucfirst($name);
    }
    else {
      $name = substr($name, 1);
      $getter = '_get' . ucfirst($name);
    }
    if (method_exists($this, $getter))
      return $this->$getter();
    $col = $this->accToCol($name);
    if (!in_array($col, self::getDbTable()->info(Zend_Db_Table::COLS)))
      throw new Exception(sprintf("Unknown column %s in table %s", $col, self::getDbTable()->info(Zend_Db_Table::NAME)));
    if (isset($this->_values[$col]))
      return $this->_values[$col];
    return null;
  }


  public function delete()
	{
		return self::getDbTable()->delete($this->where());
	}
  
  static public function find($id)
  {
		$result = self::getDbTable()->find($id);
		if (0 == count($result)) {
			return null;
		}
		return self::create($result->current());
  }

  static public function fetchAll($where = null, $order = null)
  {
		$resultSet = self::getDbTable()->fetchAll($where, $order);
		$entries   = array();
		foreach ($resultSet as $row) {
			$entries[] = self::create($row);
		}
		return $entries;
  }

	static public function fetchRow($where = null, $order = null)
  {
		$row = self::getDbTable()->fetchRow($where, $order);
		if ($row === null) {
			return null;
		}
		return self::create($row);
  }

  public function dateValue($date)
  {
    $formats = array(Zend_Date::DATETIME_LONG, Zend_Date::DATE_LONG, Zend_Date::DATETIME_SHORT, Zend_Date::DATE_SHORT, Zend_Date::W3C);
    foreach ($formats as $format)
      if (Zend_Date::isDate($date, $format))
        return new Zend_Date($date, $format);
    return null;
  }
}
