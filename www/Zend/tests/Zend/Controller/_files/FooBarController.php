<?php
require_once 'PHPUnit/Util/Filter.php';

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

require_once 'Zend/Controller/Action.php';

class FooBarController extends Zend_Controller_Action
{
    public function bazBatAction()
    {
        $this->render();
    }
}
