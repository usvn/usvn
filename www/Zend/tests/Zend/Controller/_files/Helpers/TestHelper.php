<?php
require_once 'PHPUnit/Util/Filter.php';
PHPUnit_Util_Filter::addFileToFilter(__FILE__);

class MyApp_TestHelper extends Zend_Controller_Action_Helper_Abstract
{
    public function direct()
    {
        $this->getResponse()->appendBody('running direct call');
    }
}
