<?php
require_once 'PHPUnit/Util/Filter.php';

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

require_once 'Zend/Controller/Action.php';

class ObController extends Zend_Controller_Action
{
    public function indexAction()
    {
        echo "OB index action called\n";
    }

    public function exceptionAction()
    {
        echo "In exception action\n";
        $view = new Zend_View();
        $view->addBasePath(dirname(dirname(__FILE__)) . '/views');
        $view->render('ob.phtml');
    }
}
