<?php

/**
* 
*/
class Default_Model_DbTable extends Zend_Db_Table_Abstract
{
    function __construct($model, $config = array())
		{
			$this->_name = $model;
			parent::__construct($config);
		}

		public function _setup($config = array())
		{
			parent::_setup($config);
		}
}
