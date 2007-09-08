<?php
require_once 'PHPUnit/Util/Filter.php';

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

require_once 'Zend/Controller/Action.php';

class IndexController extends Zend_Controller_Action
{
    public function indexAction()
    {
        $this->_response->appendBody("Index action called\n");
    }

    public function prefixAction()
    {
        $this->_response->appendBody("Prefix action called\n");
    }

    public function argsAction()
    {
        $args = '';
        foreach ($this->getInvokeArgs() as $key => $value) {
            $args .= $key . ': ' . $value . '; ';
        }
        $this->_response->appendBody('Args action called with params ' . $args . "\n");
    }

    public function replaceAction()
    {
        $request = new Zend_Controller_Request_Http();
        $request->setControllerName('index')
                ->setActionName('reset')
                ->setDispatched(false);
        $response = new Zend_Controller_Response_Http();
        $front = Zend_Controller_Front::getInstance();
        $front->setRequest($request)
              ->setResponse($response);
    }

    public function resetAction()
    {
        $this->_response->appendBody('Reset action called');
    }
}
