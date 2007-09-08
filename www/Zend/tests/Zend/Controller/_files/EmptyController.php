<?php
require_once 'PHPUnit/Util/Filter.php';

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

require_once 'Zend/Controller/Action.php';

class EmptyController extends Zend_Controller_Action
{
    public function indexAction()
    {}
}
