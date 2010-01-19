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
  private $_lastSaveErrors = null;

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

  public function getLastSaveErrors()
  {
    return $this->_lastSaveErrors;
  }

  public function validateBeforeSave()
  {
    return array();
  }

  public function save()
  {
    $errors = $this->validateBeforeSave();
    if (!empty($errors))
    {
      $this->_lastSaveErrors = $errors;
      return false;
    }
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
    foreach ($pKey as $i)
      $where["{$i} = ?"] = $this->_values[$i];
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

  public function updateWithValues($values)
  {
    foreach ($values as $name => $value)
    {
      $this->_values[$name] = $value;
    }
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

  protected function _initNew(array $theValues)
  {
    foreach ($theValues as $name => $value) {
      $this->_values[$name] = $value;
    }
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
    preg_match_all('!^[a-z]+|[A-Z][a-z]*!', $name, &$match);
    foreach ($match[0] as &$value)
      $value = strtolower($value);
    $name = join($match[0], '_');
    USVNLogObject('name', $name);
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
    if ($name[0] != '_') {
      $setter = 'set' . ucfirst($name);
    }
    else {
      $name = substr($name, 1);
      $setter = '_set' . ucfirst($name);
    }
    if (method_exists($this, $setter))
      $this->$setter($value);
    $this->_values[$this->accToCol($name)] = $value;
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
    $col = $this->accToCol($name);
    if (isset($this->_values[$col]))
      return $this->_values[$col];
    if (!in_array($col, self::getDbTable()->info(Zend_Db_Table::COLS)))
      throw new Exception(sprintf("Unknown column %s in table %s", $col, self::getDbTable()->info(Zend_Db_Table::NAME)));
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
}
