<?php
/**
 * Generate edit link
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
class USVN_View_Helper_EditLink {
    /**
     * Generates edit link
     *
     * @access public
     *
     * @param string Type of ressource to edit (ex: project)
     * @param string Name of ressource
     *
     * @return string HTML link: <a href="test">Test</a>.
     */
    public function editLink($type, $name)
    {
        $front = Zend_Controller_Front::getInstance();
        $view = $front->getParam('view');
        $url = $view->url(array('action' => 'edit', 'name' => $name));
        $text = T_("Edit");
        return <<< EOF
        <a href="{$url}">{$text}</a>
EOF;
    }
}
