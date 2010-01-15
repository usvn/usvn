<?php

/**
* 
*/
class Default_Model_DbTable extends Zend_Db_Table_Abstract
{
    function __construct($config = array())
		{
		  if (is_string($config))
  		  $config = array(Zend_Db_Table::NAME => $config);
			parent::__construct($config);
		}

		public function _setup($config = array())
		{
			parent::_setup($config);
		}

		public function getColumns()
		{
		  parent::_getCols();
		}
}
