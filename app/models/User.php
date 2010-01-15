<?php

class Default_Model_User extends Default_Model_Abstract
{
  static public function getDbTableConfig()
  {
    $config = parent::getDbTableConfig();
    $config[self::PRIMARY] = array('users_id');
     // case self::ROW_CLASS:
     //     $this->setRowClass($value);
     //     break;
     // case self::REFERENCE_MAP:
     //     $this->setReferences($value);
     //     break;
     // case self::DEPENDENT_TABLES:
     //     $this->setDependentTables($value);
     //     break;
  }

  static public function getFields()
  {
    return array_push(
      parent::getFields(),
      ''
    );
  }
  
    protected function _initNew(array $values)
    {
      parent::_initNew($values);
    }

    protected function _initWithRow(Zend_Db_Table_Row $row)
    {
      parent::_initWithRow($values);
    }

    public function save()
    {
      $this->getMapper()->save($this);
    }
    
    public function delete()
    {
      $this->getMapper()->delete($this);
    }

    static public function find($id)
    {
      return self::getMapper()->find($id);
    }

    static public function fetchAll($where = null, $order = null)
    {
      return self::getMapper()->fetchAll($where, $order);
    }
    
    static public function fetchRow($where = null, $order = null)
    {
      return self::getMapper()->fetchRow($where, $order);
    }
    
    public function tickets()
    {
      return Default_Model_Ticket::fetchAll("milestone_id = '{$this->_id}'");
    }
}
