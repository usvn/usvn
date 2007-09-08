<?php
require_once 'PHPUnit/Util/Filter.php';

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

require_once 'Zend/Controller/Action.php';

class Foo_Admin_IndexController extends Zend_Controller_Action
{
    public function indexAction()
    {
        $this->_response->appendBody("Foo_Admin_IndexController::indexAction() called\n");
    }
}
