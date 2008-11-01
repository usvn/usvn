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
 * @package    Zend_Form
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/** Zend_Form_Element_Xhtml */
require_once 'Zend/Form/Element/Xhtml.php';

/**
 * Zend_Form_Element 
 * 
 * @category   Zend
 * @package    Zend_Form
 * @subpackage Element
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: File.php 11705 2008-10-06 19:51:46Z thomas $
 */
class Zend_Form_Element_File extends Zend_Form_Element_Xhtml
{
    /**
     * @const string Plugin loader type
     */
    const TRANSFER_ADAPTER = 'TRANSFER_ADAPTER';

    /**
     * @var string Default view helper
     */
    public $helper = 'formFile';

    /**
     * @var Zend_File_Transfer_Adapter_Abstract
     */
    protected $_adapter;

    /**
     * @var boolean Already validated ?
     */
    protected $_validated = false;

    /**
     * @var integer Internal multifile counter
     */
    protected $_counter = 1;

    /**
     * @var integer Maximum file size for MAX_FILE_SIZE attribut of form
     */
    protected static $_maxFileSize = 0;

    /**
     * Load default decorators
     * 
     * @return void
     */
    public function loadDefaultDecorators()
    {
        if ($this->loadDefaultDecoratorsIsDisabled()) {
            return;
        }

        $decorators = $this->getDecorators();
        if (empty($decorators)) {
            $this->addDecorator('File')
                 ->addDecorator('Errors')
                 ->addDecorator('HtmlTag', array('tag' => 'dd'))
                 ->addDecorator('Label', array('tag' => 'dt'));
        }
    }

    /**
     * Set plugin loader
     * 
     * @param  Zend_Loader_PluginLoader_Interface $loader 
     * @param  string $type 
     * @return Zend_Form_Element_File
     */
    public function setPluginLoader(Zend_Loader_PluginLoader_Interface $loader, $type)
    {
        $type = strtoupper($type);

        if ($type != self::TRANSFER_ADAPTER) {
            return parent::setPluginLoader($loader, $type);
        }

        $this->_loaders[$type] = $loader;
        return $this;
    }

    /**
     * Get Plugin Loader
     * 
     * @param  string $type 
     * @return Zend_Loader_PluginLoader_Interface
     */
    public function getPluginLoader($type)
    {
        $type = strtoupper($type);

        if ($type != self::TRANSFER_ADAPTER) {
            return parent::getPluginLoader($type);
        }

        if (!array_key_exists($type, $this->_loaders)) {
            require_once 'Zend/Loader/PluginLoader.php';
            $loader = new Zend_Loader_PluginLoader(array(
                'Zend_File_Transfer_Adapter' => 'Zend/File/Transfer/Adapter/',
            ));
            $this->setPluginLoader($loader, self::TRANSFER_ADAPTER);
        }

        return $this->_loaders[$type];
    }

    /**
     * Add prefix path for plugin loader
     * 
     * @param  string $prefix 
     * @param  string $path 
     * @param  string $type 
     * @return Zend_Form_Element_File
     */
    public function addPrefixPath($prefix, $path, $type = null)
    {
        $type = strtoupper($type);
        if (!empty($type) && ($type != self::TRANSFER_ADAPTER)) {
            return parent::addPrefixPath($prefix, $path, $type);
        }

        if (empty($type)) {
            $pluginPrefix = rtrim($prefix, '_') . '_Transfer_Adapter';
            $pluginPath   = rtrim($path, DIRECTORY_SEPARATOR) . '/Transfer/Adapter/';
            $loader    = $this->getPluginLoader(self::TRANSFER_ADAPTER);
            $loader->addPrefixPath($pluginPrefix, $pluginPath);
            return parent::addPrefixPath($prefix, $path, null);
        }

        $loader = $this->getPluginLoader($type);
        $loader->addPrefixPath($prefix, $path);
        return $this;
    }

    /**
     * Set transfer adapter
     * 
     * @param  string|Zend_File_Transfer_Adapter_Abstract $adapter 
     * @return Zend_Form_Element_File
     */
    public function setTransferAdapter($adapter)
    {
        if ($adapter instanceof Zend_File_Transfer_Adapter_Abstract) {
            $this->_adapter = $adapter;
        } elseif (is_string($adapter)) {
            $loader = $this->getPluginLoader(self::TRANSFER_ADAPTER);
            $class  = $loader->load($adapter);
            $this->_adapter = new $class;
        } else {
            require_once 'Zend/Form/Element/Exception.php';
            throw new Zend_Form_Element_Exception('Invalid adapter specified');
        }

        return $this;
    }

    /**
     * Get transfer adapter
     *
     * Lazy loads HTTP transfer adapter when no adapter registered.
     * 
     * @return Zend_File_Transfer_Adapter_Abstract
     */
    public function getTransferAdapter()
    {
        if (null === $this->_adapter) {
            $this->setTransferAdapter('Http');
        }
        return $this->_adapter;
    }

    /**
     * Add Validator; proxy to adapter
     * 
     * @param  string|Zend_Validate_Interface $validator 
     * @param  bool $breakChainOnFailure 
     * @param  mixed $options 
     * @return Zend_Form_Element_File
     */
    public function addValidator($validator, $breakChainOnFailure = false, $options = array())
    {
        $adapter = $this->getTransferAdapter();
        $adapter->addValidator($validator, $breakChainOnFailure, $options, $this->getName());
        $this->_validated = false;

        return $this;
    }

    /**
     * Add multiple validators at once; proxy to adapter
     * 
     * @param  array $validators 
     * @return Zend_Form_Element_File
     */
    public function addValidators(array $validators)
    {
        $adapter = $this->getTransferAdapter();
        $adapter->addValidators($validators, $this->getName());
        $this->_validated = false;

        return $this;
    }

    /**
     * Add multiple validators at once, overwriting; proxy to adapter
     * 
     * @param  array $validators 
     * @return Zend_Form_Element_File
     */
    public function setValidators(array $validators)
    {
        $adapter = $this->getTransferAdapter();
        $adapter->setValidators($validators, $this->getName());
        $this->_validated = false;

        return $this;
    }

    /**
     * Retrieve validator by name; proxy to adapter
     * 
     * @param  string $name 
     * @return Zend_Validate_Interface|null
     */
    public function getValidator($name)
    {
        $adapter = $this->getTransferAdapter();
        return $adapter->getValidator($name);
    }

    /**
     * Retrieve all validators; proxy to adapter
     * 
     * @return array
     */
    public function getValidators()
    {
        $adapter = $this->getTransferAdapter();
        return $adapter->getValidators($this->getName());
    }

    /**
     * Remove validator by name; proxy to adapter
     * 
     * @param  string $name 
     * @return Zend_Form_Element_File
     */
    public function removeValidator($name)
    {
        $adapter = $this->getTransferAdapter();
        $adapter->removeValidator($name);
        $this->_validated = false;

        return $this;
    }

    /**
     * Remove all validators; proxy to adapter
     * 
     * @return Zend_Form_Element_File
     */
    public function clearValidators()
    {
        $adapter = $this->getTransferAdapter();
        $adapter->clearValidators();
        $this->_validated = false;

        return $this;
    }

    /**
     * Add Filter; proxy to adapter
     * 
     * @param  string|array $filter  Type of filter to add
     * @param  string|array $options Options to set for the filter
     * @return Zend_Form_Element_File
     */
    public function addFilter($filter, $options = null)
    {
        $adapter = $this->getTransferAdapter();
        $adapter->addFilter($filter, $options, $this->getName());

        return $this;
    }

    /**
     * Add Multiple filters at once; proxy to adapter
     * 
     * @param  array $filters 
     * @return Zend_Form_Element_File
     */
    public function addFilters(array $filters)
    {
        $adapter = $this->getTransferAdapter();
        $adapter->addFilters($filters, $this->getName());

        return $this;
    }

    /**
     * Sets a filter for the class, erasing all previous set; proxy to adapter
     *
     * @param  string|array $filter Filter to set
     * @return Zend_Form_Element_File
     */
    public function setFilters(array $filters)
    {
        $adapter = $this->getTransferAdapter();
        $adapter->setFilters($filters, $this->getName());

        return $this;
    }

    /**
     * Retrieve individual filter; proxy to adapter
     * 
     * @param  string $name 
     * @return Zend_Filter_Interface|null
     */
    public function getFilter($name)
    {
        $adapter = $this->getTransferAdapter();
        return $adapter->getFilter($name);
    }

    /**
     * Returns all set filters; proxy to adapter
     *
     * @return null|array List of set filters
     */
    public function getFilters()
    {
        $adapter = $this->getTransferAdapter();
        return $adapter->getFilters($this->getName());
    }

    /**
     * Remove an individual filter; proxy to adapter 
     * 
     * @param  string $name 
     * @return Zend_Form_Element_File
     */
    public function removeFilter($name)
    {
        $adapter = $this->getTransferAdapter();
        $adapter->removeFilter($name);

        return $this;
    }

    /**
     * Remove all filters; proxy to adapter
     * 
     * @return Zend_Form_Element_File
     */
    public function clearFilters()
    {
        $adapter = $this->getTransferAdapter();
        $adapter->clearFilters();

        return $this;
    }

    /**
     * Validate upload
     * 
     * @param  string $value   File, can be optional, give null to validate all files
     * @param  mixed  $context 
     * @return bool
     */
    public function isValid($value, $context = null)
    {
        if ($this->_validated) {
            return true;
        }

        $adapter = $this->getTransferAdapter();

        if (!$this->isRequired()) {
            $adapter->setOptions(array('ignoreNoFile' => true));
        } else {
            $adapter->setOptions(array('ignoreNoFile' => false));
            if ($this->autoInsertNotEmptyValidator() and
                   !$this->getValidator('NotEmpty'))
            {
                $validators = $this->getValidators();
                $notEmpty   = array('validator' => 'NotEmpty', 'breakChainOnFailure' => true);
                array_unshift($validators, $notEmpty);
                $this->setValidators($validators);
            }
        }

        if($adapter->isValid($this->getName())) {
            $this->_validated = true;
            return true;
        }

        $this->_validated = false;
        return false;
    }

    /**
     * Receive the uploaded file
     *
     * @param  string $value
     * @return boolean
     */
    public function receive($value = null)
    {
        if (!$this->_validated) {
            if (!$this->isValid($value)) {
                return false;
            }
        }

        $adapter = $this->getTransferAdapter();
        if ($adapter->receive($this->getName())) {
            return true;
        }

        return false;
    }

    /**
     * Retrieve error codes; proxy to transfer adapter
     * 
     * @return array
     */
    public function getErrors()
    {
        return $this->getTransferAdapter()->getErrors();
    }

    /**
     * Are there errors registered?
     * 
     * @return bool
     */
    public function hasErrors()
    {
        return $this->getTransferAdapter()->hasErrors();
    }

    /**
     * Retrieve error messages; proxy to transfer adapter
     * 
     * @return array
     */
    public function getMessages()
    {
        return $this->getTransferAdapter()->getMessages();
    }

    /**
     * Set the upload destination
     * 
     * @param  string $path 
     * @return Zend_Form_Element_File
     */
    public function setDestination($path)
    {
        $this->getTransferAdapter()->setDestination($path, $this->getName());
        return $this;
    }

    /**
     * Get the upload destination
     * 
     * @return string
     */
    public function getDestination()
    {
        return $this->getTransferAdapter()->getDestination($this->getName());
    }

    /**
     * Get the final filename
     * 
     * @param  string $value (Optional) Element or file to return
     * @return string
     */
    public function getFileName($value = null)
    {
        if (empty($value)) {
            $value = $this->getName();
        }
        return $this->getTransferAdapter()->getFileName($value);
    }

    /**
     * Set a multifile element
     *
     * @param integer $count Number of file elements
     * @return Zend_Form_Element_File Provides fluid interface
     */
    public function setMultiFile($count)
    {
        if ((integer) $count < 2) {
            $this->setIsArray(false);
            $this->_counter = 1;
        } else {
            $this->setIsArray(true);
            $this->_counter = (integer) $count;
        }

        return $this;
    }

    /**
     * Returns the multifile element number
     *
     * @return integer
     */
    public function getMultiFile()
    {
        return $this->_counter;
    }

    /**
     * Sets the maximum file size of the form
     *
     * @param  integer $size
     * @return integer
     */
    public function setMaxFileSize($size)
    {
        self::$_maxFileSize = $size;
        return $this;
    }

    /**
     * Sets the maximum file size of the form
     *
     * @return integer
     */
    public function getMaxFileSize()
    {
        return self::$_maxFileSize;
    }
}
