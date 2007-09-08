<?php
require_once 'PHPUnit/Util/Filter.php';
PHPUnit_Util_Filter::addFileToFilter(__FILE__);

require_once 'Zend/Controller/Action.php';

class HelperBrokerController extends Zend_Controller_Action 
{
    
    
    public function testGetRedirectorAction()
    {
        $redirector = $this->_helper->getHelper('Redirector');
        $this->getResponse()->appendBody(get_class($redirector));
    }

    public function testHelperViaMagicGetAction()
    {
        $redirector = $this->_helper->Redirector;
        $this->getResponse()->appendBody(get_class($redirector));
    }
    
    public function testHelperViaMagicCallAction()
    {
        $this->getResponse()->appendBody($this->_helper->TestHelper());
    }
    
    public function testBadHelperAction()
    {
        try {
            $this->_helper->getHelper('NonExistentHelper');
        } catch (Exception $e) {
            $this->getResponse()->appendBody($e->getMessage());
        }
        
    }
    

    
    public function testCustomHelperAction()
    {
        $this->getResponse()->appendBody(get_class($this->_helper->TestHelper));
    }
    
}
