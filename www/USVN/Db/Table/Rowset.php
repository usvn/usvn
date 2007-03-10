<?php

/**
 * @category   USVN
 * @package    USVN_Db
 * @subpackage Table
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @todo also implement Countable if ZF minimum PHP version moves up to 5.1.
 */
class USVN_Db_Table_Rowset extends Zend_Db_Table_Rowset implements Iterator {

    /**
     * Return the current element.
     * Similar to the current() function for arrays in PHP
     *
     * @return mixed current element from the collection
     */
    public function current()
    {
        // is the pointer at a valid position?
        if (! $this->valid()) {
            return false;
        }

        // do we already have a row object for this position?
        if (empty($this->_rows[$this->_pointer])) {
            // create a row object
            $this->_rows[$this->_pointer] = new $this->_info[tableRowName](array(
                'db'    => $this->_db,
                'table' => $this->_table,
                'data'  => $this->_data[$this->_pointer]
            ));
        }

        // return the row object
        return $this->_rows[$this->_pointer];
    }

}
