<?php
class USVN_View_Helper_Img {
    /**
    * @param Path to thie image without medias/TEMPLATE/images/
    * @param Alternative text (empty by default)
    */
    public function img($path, $alt = "")
    {
        $ctrl = Zend_Controller_Front::getInstance();
        return '<img src="' . $ctrl->getBaseUrl() . '/medias/default/images/' . $path . '" alt="' . $alt .'" />';
    }
}
