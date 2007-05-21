<?php
class USVN_View_Helper_UrlConfirm extends Zend_View_Helper_Url {
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
