<?php
require_once 'PHPUnit/Util/Filter.php';

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

require_once 'Zend/Controller/Action.php';

class Bar_IndexController extends Zend_Controller_Action
{
    public function indexAction()
    {
        $this->_response->appendBody("Bar_IndexController::indexAction() called\n");
    }

    public function testAction()
    {
    }
}
