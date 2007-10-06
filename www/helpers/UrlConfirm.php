<?php
/**
 * Generate html link with confirmation
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
class USVN_View_Helper_UrlConfirm extends USVN_View_Helper_Url {
    /**
     * Generates an url given the name of a route.
     *
     * @access public
     *
     * @param array $urlOptions Options passed to the assemble method of the Route object.
     * @param string Text of the link
     * @param string Text of the confirmation
     * @param mixed $name The name of a Route to use. If null it will use the current Route
     *
     * @return string HTML link: <a href="test">Test</a>.
     */
    public function urlConfirm($urlOptions, $text, $confirmText, $name = null)
    {
        $url = $this->url($urlOptions, $name);
        return <<< EOF
        <a href="{$url}" onclick="return confirm('{$confirmText}')">
            {$text}
        </a>
EOF;
    }
}
