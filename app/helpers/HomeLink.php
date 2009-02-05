<?php
/**
 * Generate home link
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.6
 * @package helper
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This project has been realised as part of
 * end of studies project.
 *
 * $Id$
 */
class USVN_View_Helper_HomeLink {
    /**
     * Generates home link
     *
     * @access public
     *
     * @param string Name of ressource
     *
     * @return string HTML link: <a href="test">Test</a>.
     */
    public function homeLink($name)
    {
        $front = Zend_Controller_Front::getInstance();
        $view = Zend_Controller_Action_HelperBroker::getExistingHelper('viewRenderer')->view;
        $url = $view->url(array('controller' => 'project', 'action' => null, 'project' => $name), 'project', true);
        $img = $view->img("CrystalClear/16x16/actions/gohome.png", T_("Home"));
        return <<< EOF
        <a href="{$url}">
            {$img}
        </a>
EOF;
    }
}
