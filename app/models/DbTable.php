<?php

/**
* 
*/
class Default_Model_DbTable extends Zend_Db_Table_Abstract
{
    function __construct($definitionOrName)
    {
        // $this->_primary = array('id');
        // $this->_cols = array('id', 'title', 'comment');
        //   if (is_string($definitionOrName))
        //     $definitionOrName = array(Zend_Db_Table_Abstract::NAME => $definitionOrName);
        //   /* TMP DEV */
        //   $definitionOrName[Zend_Db_Table_Abstract::NAME] = 'usvn_' . $definitionOrName[Zend_Db_Table_Abstract::NAME];
        //   /* !TMP DEV */
        //   // if (!isset($definitionOrName[Zend_Db_Table_Abstract::PRIMARY]))
        //   //     $definitionOrName[Zend_Db_Table_Abstract::PRIMARY] = array('id');
        parent::__construct();
    }

    public function _setup($config = array())
    {
        $this->_name = 'usvn_tickets';
        parent::_setup($config);
    }
}
