<?php
require_once 'PHPUnit/Util/Filter.php';

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

require_once 'Zend/Controller/Action.php';

class ViewController extends Zend_Controller_Action
{
    public function indexAction()
    {
        $this->render();
    }

    public function testAction()
    {
        $this->render('index');
    }

    public function siteAction()
    {
        $this->render('site', null, true);
    }

    public function nameAction()
    {
        $this->render(null, 'name');
    }

    public function scriptAction()
    {
        $this->renderScript('custom/renderScript.php');
    }

    public function scriptNameAction()
    {
        $this->renderScript('custom/renderScript.php', 'foo');
    }
}
