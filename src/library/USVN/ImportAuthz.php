<?php


class USVN_ImportAuthz
{
    private $_file;
    public function __construct($file)
    {
        $this->_file = $file;

        USVN_Authz::load($this->_file);
        USVN_Authz::generate();
    }
}
