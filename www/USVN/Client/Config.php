<?php
/**
 * Manipulate client config file
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.5
 * @package client
 * @subpackage console
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This project has been realised as part of
 * end of studies project.
 *
 * $Id$
 */
require_once 'USVN/Exception.php';

/**
* Manipulate the config file. If the the config doesn't exists, it will be create.
*/
class USVN_Client_Config
{
    private $config_path;
    private $xml;

    /**
    * @var path of svn repository
    */
    public function USVN_Client_Config($path)
    {
        $this->config_path = $path.'/usvn/config.xml';
        $this->xml = @simplexml_load_file($this->config_path);
        if (!$this->xml)
        {
            $this->xml =  new SimpleXMLElement("<usvn></usvn>");
        }
    }

    /**
    * Write the config file
    */
    public function save()
    {
        if (!@file_put_contents($this->config_path, $this->xml->asXml()))
        {
                throw new USVN_Exception("Can't write config file.");
        }
    }

    /**
    * Access to config var
    *
    * @example $config->url
    * @return string
    */
    public function __get($member)
    {
        return (string)$this->xml->$member;
    }

    /**
    * Set config var
    *
    * @example $config->url = 'http://www.usvn.info'
    */
    public function __set($member, $value)
    {
        $this->xml->$member = $value;
    }
}
