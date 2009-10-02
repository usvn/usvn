<?php

/**
* 
*/
class Default_Model_DbTable extends Zend_Db_Table_Abstract
{
    function __construct($model)
		{
			$this->_name = $model;
			parent::__construct();
		}

		public function _setup($config = array())
		{
			parent::_setup($config);
		}
}
