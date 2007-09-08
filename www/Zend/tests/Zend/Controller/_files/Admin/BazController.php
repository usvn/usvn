<?php
require_once 'PHPUnit/Util/Filter.php';

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

require_once 'Zend/Controller/Action.php';

class Admin_BazController extends Zend_Controller_Action
{
    public function preDispatch()
    {
        $this->_response->appendBody("preDispatch called\n");
    }

    public function postDispatch()
    {
        $this->_response->appendBody("postDispatch called\n");
    }

    public function barAction()
    {
        $this->_response->appendBody("Admin's Baz::bar action called\n");
    }
}
