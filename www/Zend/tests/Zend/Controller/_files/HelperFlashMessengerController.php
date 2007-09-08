<?php

require_once 'Zend/Controller/Action.php';

class HelperFlashMessengerController extends Zend_Controller_Action 
{
    
    public function indexAction()
    {
        $flashmessenger = $this->_helper->FlashMessenger;
        $this->getResponse()->appendBody(get_class($flashmessenger));
        
        $messages = $flashmessenger->getCurrentMessages();
        if (count($messages) == 0) {
            $this->getResponse()->appendBody('1');
        }
        
        $flashmessenger->addMessage('My message');
        $messages = $flashmessenger->getCurrentMessages();
        
        if (implode('', $messages) == 'My message') {
            $this->getResponse()->appendBody('2');
        }
        
        if ($flashmessenger->count() == 0) {
            $this->getResponse()->appendBody('3');
        }
        
        if ($flashmessenger->hasMessages() === false) {
            $this->getResponse()->appendBody('4');
        }
        
        if ($flashmessenger->getRequest() == $this->getRequest()) {
            $this->getResponse()->appendBody('5');
        }
        
        if ($flashmessenger->getResponse() == $this->getResponse()) {
            $this->getResponse()->appendBody('6');
        }
        
    }
    
}