<?php

/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @see Zend_Gdata_App_FeedEntryParent
 */
require_once 'Zend/Gdata/App/FeedEntryParent.php';

/**
 * @see Zend_Gdata_App_Extension_Content
 */
require_once 'Zend/Gdata/App/Extension/Content.php';

/**
 * @see Zend_Gdata_App_Extension_Published
 */
require_once 'Zend/Gdata/App/Extension/Published.php';

/**
 * @see Zend_Gdata_App_Extension_Source
 */
require_once 'Zend/Gdata/App/Extension/Source.php';

/**
 * @see Zend_Gdata_App_Extension_Summary
 */
require_once 'Zend/Gdata/App/Extension/Summary.php';

/**
 * @see Zend_Gdata_App_Extension_Control
 */
require_once 'Zend/Gdata/App/Extension/Control.php';

/**
 * Concrete class for working with Atom entries.
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Gdata_App_Entry extends Zend_Gdata_App_FeedEntryParent
{

    /**
     * Root XML element for Atom entries.
     *
     * @var string
     */
    protected $_rootElement = 'entry';

    /**
     * Class name for each entry in this feed* 
     *
     * @var string
     */
    protected $_entryClassName = 'Zend_Gdata_App_Entry';

    /**
     * atom:content element
     *
     * @var Zend_Gdata_App_Extension_Content 
     */
    protected $_content = null;

    /**
     * atom:published element
     *
     * @var Zend_Gdata_App_Extension_Published
     */
    protected $_published = null;

    /**
     * atom:source element
     *
     * @var Zend_Gdata_App_Extension_Source
     */
    protected $_source = null;

    /**
     * atom:summary element
     *
     * @var Zend_Gdata_App_Extension_Summary
     */
    protected $_summary = null;

    /**
     * app:control element
     *
     * @var Zend_Gdata_App_Extension_Control
     */
    protected $_control = null;

    public function getDOM($doc = null)
    {
        $element = parent::getDOM($doc);
        if ($this->_content != null) {
            $element->appendChild($this->_content->getDOM($element->ownerDocument));
        }
        if ($this->_published != null) {
            $element->appendChild($this->_published->getDOM($element->ownerDocument));
        }
        if ($this->_source != null) {
            $element->appendChild($this->_source->getDOM($element->ownerDocument));
        }
        if ($this->_summary != null) {
            $element->appendChild($this->_summary->getDOM($element->ownerDocument));
        }
        if ($this->_control != null) {
            $element->appendChild($this->_control->getDOM($element->ownerDocument));
        }
        return $element;
    }

    protected function takeChildFromDOM($child)
    {
        $absoluteNodeName = $child->namespaceURI . ':' . $child->localName;
        switch ($absoluteNodeName) {
        case $this->lookupNamespace('atom') . ':' . 'content':
            $content = new Zend_Gdata_App_Extension_Content();
            $content->transferFromDOM($child);
            $this->_content = $content;
            break;
        case $this->lookupNamespace('atom') . ':' . 'published':
            $published = new Zend_Gdata_App_Extension_Published();
            $published->transferFromDOM($child);
            $this->_published = $published;
            break;
        case $this->lookupNamespace('atom') . ':' . 'source':
            $source = new Zend_Gdata_App_Extension_Source();
            $source->transferFromDOM($child);
            $this->_source = $source;
            break;
        case $this->lookupNamespace('atom') . ':' . 'summary':
            $summary = new Zend_Gdata_App_Extension_Summary();
            $summary->transferFromDOM($child);
            $this->_summary = $summary;
            break;
        case $this->lookupNamespace('app') . ':' . 'control':
            $control = new Zend_Gdata_App_Extension_Control();
            $control->transferFromDOM($child);
            $this->_control = $control;
            break;
        default:
            parent::takeChildFromDOM($child);
            break;
        }
    }

    /**
     * Uploads changes in this entry to the server using the referenced 
     * Zend_Http_Client to do a HTTP PUT to the edit link stored in this 
     * entry's link collection.  Body for the PUT is generated using the
     * saveXML method (which calls getDOM).
     *
     * @return Zend_Gdata_App_Entry The updated entry
     * @throws Zend_Gdata_App_Exception
     */
    public function save()
    {
        if ($this->id) {
            // If id is set, look for link rel="edit" in the
            // entry object and PUT.
            $editLink = $this->getLink('edit');
            $editUri = $editLink->href;
            if (!$editUri) {
                throw new Zend_Gdata_App_Exception('Cannot edit entry; no link rel="edit" is present.');
            }
            $client = $this->getHttpClient();
            if (is_null($client)) {
                $client = Zend_Gdata_App::getStaticHttpClient();
            }
            $client->resetParameters();
            $client->setUri($editUri);
            if (Zend_Gdata_App::getHttpMethodOverride()) {
                $client->setHeaders(array('X-HTTP-Method-Override: PUT',
                    'Content-Type: application/atom+xml'));
                $client->setRawData($this->saveXML());
                $response = $client->request('POST');
            } else {
                $client->setHeaders('Content-Type', 'application/atom+xml');
                $client->setRawData($this->saveXML());
                $response = $client->request('PUT');
            }
            if ($response->getStatus() !== 200) {
                require_once 'Zend/Gdata/App/HttpException.php';
                $exception = new Zend_Gdata_App_HttpException('Expected response code 200, got ' . $response->getStatus());
                $exception->setResponse($response);
                throw $exception;
            }
            // Update internal properties using $client->responseBody;
            $entryClassName = get_class($this);
            $returnEntry = new $entryClassName($response->getBody());
            $returnEntry->setHttpClient($client);
            return $returnEntry;
        } else {
            throw new Zend_Gdata_App_Exception('Cannot edit entry; no id is present');
        }
    }

    /**
     * Deletes this entry to the server using the referenced 
     * Zend_Http_Client to do a HTTP DELETE to the edit link stored in this 
     * entry's link collection.  
     *
     * @return boolean The success of the delete operation
     * @throws Zend_Gdata_App_Exception
     */
    public function delete()
    {
        if ($this->id) {
            // If id is set, look for link rel="edit" in the
            // entry object and DELETE.
            $editLink = $this->getLink('edit');
            $editUri = $editLink->href;
            if (!$editUri) {
                throw new Zend_Gdata_App_Exception('Cannot delete entry; no link rel="edit" is present.');
            }
            $client = $this->getHttpClient();
            if (is_null($client)) {
                $client = Zend_Gdata_App::getStaticHttpClient();
            }
            $client->resetParameters();
            $client->setUri($editUri);
            $client->setHeaders('Content-Type', null);
            $client->setRawData('');
            if (Zend_Gdata_App::getHttpMethodOverride()) {
                $client->setHeaders('X-HTTP-Method-Override', 'DELETE');
                $response = $client->request('DELETE');
            } else {
                $response = $client->request('DELETE');
            }
            if ($response->getStatus() !== 200) {
                require_once 'Zend/Gdata/App/HttpException.php';
                $exception = new Zend_Gdata_App_HttpException('Expected response code 200, got ' . $response->getStatus());
                $exception->setResponse($response);
                throw $exception;
            }
            return true;
        } else {
            throw new Zend_Gdata_App_Exception('Cannot edit entry; no id is present');
        }
    }

    /**
     * @return Zend_Gdata_App_Extension_Content 
     */
    public function getContent()
    {
        return $this->_content;
    }

    /**
     * @param Zend_Gdata_App_Extension_Content $value 
     * @return Zend_Gdata_App_Entry Provides a fluent interface
     */
    public function setContent($value)
    {
        $this->_content = $value;
        return $this; 
    }

    /**
     * @return Zend_Gdata_App_Extension_Published 
     */
    public function getPublished()
    {
        return $this->_published;
    }

    /**
     * @param Zend_Gdata_App_Extension_Published $value 
     * @return Zend_Gdata_App_Entry Provides a fluent interface
     */
    public function setPublished($value)
    {
        $this->_published = $value;
        return $this; 
    }

    /**
     * @return Zend_Gdata_App_Extension_Source 
     */
    public function getSource()
    {
        return $this->_source;
    }

    /**
     * @param Zend_Gdata_App_Extension_Source $value 
     * @return Zend_Gdata_App_Entry Provides a fluent interface
     */
    public function setSource($value)
    {
        $this->_source = $value;
        return $this; 
    }

    /**
     * @return Zend_Gdata_App_Extension_Summary 
     */
    public function getSummary()
    {
        return $this->_summary;
    }

    /**
     * @param Zend_Gdata_App_Extension_Summary $value 
     * @return Zend_Gdata_App_Entry Provides a fluent interface
     */
    public function setSummary($value)
    {
        $this->_summary = $value;
        return $this; 
    }

    /**
     * @return Zend_Gdata_App_Extension_Control 
     */
    public function getControl()
    {
        return $this->_control;
    }

    /**
     * @param Zend_Gdata_App_Extension_Control $value 
     * @return Zend_Gdata_App_Entry Provides a fluent interface
     */
    public function setControl($value)
    {
        $this->_control = $value;
        return $this; 
    }
}
