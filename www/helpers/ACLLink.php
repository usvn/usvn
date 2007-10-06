<?php
/**
 * Generate link to project acl
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.5
 * @package helper
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This project has been realised as part of
 * end of studies project.
 *
 * $Id$
 */
class USVN_View_Helper_ACLLink {
    /**
     * Generates acl link
     *
     * @access public
     *
     * @param string Project name
     *
     * @return string HTML link: <a href="test">Test</a>.
     */
    public function ACLLink($project)
    {
        $front = Zend_Controller_Front::getInstance();
        $view = Zend_Controller_Action_HelperBroker::getExistingHelper('viewRenderer')->view;
        $img = $view->img("CrystalClear/16x16/apps/kwalletmanager.png", T_('Rights'));
        return '<a href="' . $view->url(array('controller' => 'browser', 'project' => $project), "project", true) . '">' . $img .'</a>';
    }
}
