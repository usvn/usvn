<?php

class Default_Model_User extends Default_Model_Abstract
{
  static public function getDbTableConfig()
  {
    $config = parent::getDbTableConfig();
    case self::ADAPTER:
         $this->_setAdapter($value);
         break;
     case self::DEFINITION:
         $this->setDefinition($value);
         break;
     case self::DEFINITION_CONFIG_NAME:
         $this->setDefinitionConfigName($value);
         break;
     case self::SCHEMA:
         $this->_schema = (string) $value;
         break;
     case self::NAME:
         $this->_name = (string) $value;
         break;
     case self::PRIMARY:
         $this->_primary = (array) $value;
         break;
     case self::ROW_CLASS:
         $this->setRowClass($value);
         break;
     case self::ROWSET_CLASS:
         $this->setRowsetClass($value);
         break;
     case self::REFERENCE_MAP:
         $this->setReferences($value);
         break;
     case self::DEPENDENT_TABLES:
         $this->setDependentTables($value);
         break;
     case self::METADATA_CACHE:
         $this->_setMetadataCache($value);
         break;
     case self::METADATA_CACHE_IN_CLASS:
         $this->setMetadataCacheInClass($value);
         break;
     case self::SEQUENCE:
         $this->_setSequence($value);
         break;
    
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
