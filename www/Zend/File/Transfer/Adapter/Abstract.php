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
 * @category  Zend
 * @package   Zend_File_Transfer
 * @copyright Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 * @version   $Id: $
 */

/**
 * Abstract class for file transfers (Downloads and Uploads)
 *
 * @category  Zend
 * @package   Zend_File_Transfer
 * @copyright Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zend_File_Transfer_Adapter_Abstract
{
    /**@+
     * @const string Plugin loader Constants
     */
    const FILTER    = 'FILTER';
    const VALIDATE  = 'VALIDATE';
    /**@-*/

    /**
     * Internal list of breaks
     *
     * @var array
     */
    protected $_break = array();

    /**
     * Internal list of filters
     *
     * @var array
     */
    protected $_filters = array();

    /**
     * Plugin loaders for filter and validation chains
     *
     * @var array
     */
    protected $_loaders = array();

    /**
     * Internal list of messages
     *
     * @var array
     */
    protected $_messages = array();

    /**
     * @var Zend_Translate
     */
    protected $_translator;

    /**
     * Is translation disabled?
     *
     * @var bool
     */
    protected $_translatorDisabled = false;

    /**
     * Internal validation flag
     *
     * @var boolean
     */
    protected $_validated = false;

    /**
     * Internal list of validators
     * @var array
     */
    protected $_validators = array();

    /**
     * Internal list of files
     * This array looks like this:
     *     array(form => array( - Form is the name within the form or, if not set the filename
     *         name,            - Original name of this file
     *         type,            - Mime type of this file
     *         size,            - Filesize in bytes
     *         tmp_name,        - Internalally temporary filename for uploaded files
     *         error,           - Error which has occured
     *         destination,     - New destination for this file
     *         validators,      - Set validator names for this file
     *         files            - Set file names for this file
     *     ))
     *
     * @var array
     */
    protected $_files = array();

    /**
     * TMP directory
     * @var string
     */
    protected $_tmpDir;

    /**
     * Options for file transfers
     */
    protected $_options = array(
        'ignoreNoFile' => false
    );

    /**
     * Send file
     * 
     * @param  mixed $options 
     * @return bool
     */
    abstract public function send($options = null);

    /**
     * Receive file
     * 
     * @param  mixed $options 
     * @return bool
     */
    abstract public function receive($options = null);

    /**
     * Is file sent?
     * 
     * @param  array|string|null $file 
     * @return bool
     */
    abstract public function isSent($file = null);

    /**
     * Is file received?
     * 
     * @param  array|string|null $file 
     * @return bool
     */
    abstract public function isReceived($file = null);

    /**
     * Retrieve progress of transfer
     * 
     * @return float
     */
    abstract public function getProgress();

    /**
     * Set plugin loader to use for validator or filter chain
     * 
     * @param  Zend_Loader_PluginLoader_Interface $loader 
     * @param  string $type 'filter', or 'validate'
     * @return Zend_File_Transfer_Adapter_Abstract
     * @throws Zend_File_Transfer_Exception on invalid type
     */
    public function setPluginLoader(Zend_Loader_PluginLoader_Interface $loader, $type)
    {
        $type = strtoupper($type);
        switch ($type) {
            case self::FILTER:
            case self::VALIDATE:
                $this->_loaders[$type] = $loader;
                return $this;
            default:
                require_once 'Zend/File/Transfer/Exception.php';
                throw new Zend_File_Transfer_Exception(sprintf('Invalid type "%s" provided to setPluginLoader()', $type));
        }
    }

    /**
     * Retrieve plugin loader for validator or filter chain
     *
     * Instantiates with default rules if none available for that type. Use 
     * 'filter' or 'validate' for $type.
     * 
     * @param  string $type 
     * @return Zend_Loader_PluginLoader
     * @throws Zend_File_Transfer_Exception on invalid type.
     */
    public function getPluginLoader($type)
    {
        $type = strtoupper($type);
        switch ($type) {
            case self::FILTER:
            case self::VALIDATE:
                $prefixSegment = ucfirst(strtolower($type));
                $pathSegment   = $prefixSegment;
                if (!isset($this->_loaders[$type])) {
                    $paths         = array(
                        'Zend_' . $prefixSegment . '_'     => 'Zend/' . $pathSegment . '/',
                        'Zend_' . $prefixSegment . '_File' => 'Zend/' . $pathSegment . '/File',
                    );

                    require_once 'Zend/Loader/PluginLoader.php';
                    $this->_loaders[$type] = new Zend_Loader_PluginLoader($paths);
                }
                return $this->_loaders[$type];
            default:
                require_once 'Zend/File/Transfer/Exception.php';
                throw new Zend_File_Transfer_Exception(sprintf('Invalid type "%s" provided to getPluginLoader()', $type));
        }
    }

    /**
     * Add prefix path for plugin loader
     *
     * If no $type specified, assumes it is a base path for both filters and 
     * validators, and sets each according to the following rules:
     * - filters:    $prefix = $prefix . '_Filter'
     * - validators: $prefix = $prefix . '_Validate'
     *
     * Otherwise, the path prefix is set on the appropriate plugin loader.
     * 
     * @param  string $path 
     * @return Zend_File_Transfer_Adapter_Abstract
     * @throws Zend_File_Transfer_Exception for invalid type
     */
    public function addPrefixPath($prefix, $path, $type = null)
    {
        $type = strtoupper($type);
        switch ($type) {
            case self::FILTER:
            case self::VALIDATE:
                $loader = $this->getPluginLoader($type);
                $loader->addPrefixPath($prefix, $path);
                return $this;
            case null:
                $prefix = rtrim($prefix, '_');
                $path   = rtrim($path, DIRECTORY_SEPARATOR);
                foreach (array(self::FILTER, self::VALIDATE) as $type) {
                    $cType        = ucfirst(strtolower($type));
                    $pluginPath   = $path . DIRECTORY_SEPARATOR . $cType . DIRECTORY_SEPARATOR;
                    $pluginPrefix = $prefix . '_' . $cType;
                    $loader       = $this->getPluginLoader($type);
                    $loader->addPrefixPath($pluginPrefix, $pluginPath);
                }
                return $this;
            default:
                require_once 'Zend/File/Transfer/Exception.php';
                throw new Zend_File_Transfer_Exception(sprintf('Invalid type "%s" provided to getPluginLoader()', $type));
        }
    }

    /**
     * Add many prefix paths at once
     * 
     * @param  array $spec 
     * @return Zend_File_Transfer_Exception
     */
    public function addPrefixPaths(array $spec)
    {
        if (isset($spec['prefix']) && isset($spec['path'])) {
            return $this->addPrefixPath($spec['prefix'], $spec['path']);
        } 
        foreach ($spec as $type => $paths) {
            if (is_numeric($type) && is_array($paths)) {
                $type = null;
                if (isset($paths['prefix']) && isset($paths['path'])) {
                    if (isset($paths['type'])) {
                        $type = $paths['type'];
                    }
                    $this->addPrefixPath($paths['prefix'], $paths['path'], $type);
                }
            } elseif (!is_numeric($type)) {
                if (!isset($paths['prefix']) || !isset($paths['path'])) {
                    foreach ($paths as $prefix => $spec) {
                        if (is_array($spec)) {
                            foreach ($spec as $path) {
                                if (!is_string($path)) {
                                    continue;
                                }
                                $this->addPrefixPath($prefix, $path, $type);
                            }
                        } elseif (is_string($spec)) {
                            $this->addPrefixPath($prefix, $spec, $type);
                        }
                    }
                } else {
                    $this->addPrefixPath($paths['prefix'], $paths['path'], $type);
                }
            }
        }
        return $this;
    }

    /**
     * Adds a new validator for this class
     *
     * @param  string|array $validator           Type of validator to add
     * @param  boolean      $breakChainOnFailure If the validation chain should stop an failure
     * @param  string|array $options             Options to set for the validator
     * @param  string|array $files               Files to limit this validator to
     * @return Zend_File_Transfer_Adapter
     */
    public function addValidator($validator, $breakChainOnFailure = false, $options = null, $files = null)
    {
        if ($validator instanceof Zend_Validate_Interface) {
            $name = get_class($validator);
        } elseif (is_string($validator)) {
            $name      = $this->getPluginLoader(self::VALIDATE)->load($validator);
            $validator = new $name($options);
        } else {
            require_once 'Zend/File/Transfer/Exception.php';
            throw new Zend_File_Transfer_Exception('Invalid validator provided to addValidator; must be string or Zend_Validate_Interface');
        }

        $this->_validators[$name] = $validator;
        $this->_break[$name]      = $breakChainOnFailure;

        if ($files === null) {
            $files = array_keys($this->_files);
        } else {
            if (!is_array($files)) {
                $files = array($files);
            }
        }

        foreach ($files as $key => $file) {
            if (!is_string($file)) {
                if (is_array($file) && !is_numeric($key)) {
                    $file = $key;
                } else {
                    continue;
                }
            }
            if (!array_key_exists($file, $this->_files)) {
                continue;
            }
            $this->_files[$file]['validators'][] = $name;
        }
        
        $this->_validated = false;
        return $this;
    }

    /**
     * Add Multiple validators at once
     *
     * @param  array $validators 
     * @param  string|array $files 
     * @return Zend_File_Transfer_Adapter_Abstract
     */
    public function addValidators(array $validators, $files = null)
    {
        foreach ($validators as $name => $validatorInfo) {
            if ($validatorInfo instanceof Zend_Validate_Interface) {
                $this->addValidator($validatorInfo, null, null, $files);
            } else if (is_string($validatorInfo)) {
                if (!is_int($name)) {
                    $this->addValidator($name, null, $validatorInfo, $files);
                } else {
                    $this->addValidator($validatorInfo, null, null, $files);
                }
            } else if (is_array($validatorInfo)) {
                $argc                = count($validatorInfo);
                $breakChainOnFailure = false;
                $options             = array();
                if (isset($validatorInfo['validator'])) {
                    $validator = $validatorInfo['validator'];
                    if (isset($validatorInfo['breakChainOnFailure'])) {
                        $breakChainOnFailure = $validatorInfo['breakChainOnFailure'];
                    }

                    if (isset($validatorInfo['options'])) {
                        $options = $validatorInfo['options'];
                    }

                    $this->addValidator($validator, $breakChainOnFailure, $options, $files);
                } else {
                    if (is_string($name)) {
                        $validator = $name;
                        $options   = $validatorInfo;
                        $this->addValidator($validator, $breakChainOnFailure, $options, $files);
                    } else {
                        switch (true) {
                            case (0 == $argc):
                                break;
                            case (1 <= $argc):
                                $validator  = array_shift($validatorInfo);
                            case (2 <= $argc):
                                $breakChainOnFailure = array_shift($validatorInfo);
                            case (3 <= $argc):
                                $options = array_shift($validatorInfo);
                            case (4 <= $argc):
                                $files = array_shift($validatorInfo);
                            default:
                                $this->addValidator($validator, $breakChainOnFailure, $options, $files);
                                break;
                        }
                    }
                }
            } else {
                require_once 'Zend/Form/Exception.php';
                throw new Zend_Form_Exception('Invalid validator passed to addValidators()');
            }
        }

        return $this;
    }

    /**
     * Sets a validator for the class, erasing all previous set
     *
     * @param  string|array $validator Validator to set
     * @param  string|array $files     Files to limit this validator to
     * @return Zend_File_Transfer_Adapter
     */
    public function setValidators(array $validators, $files = null)
    {
        $this->clearValidators();
        return $this->addValidators($validators, $files);
    }

    /**
     * Determine if a given validator has already been registered
     * 
     * @param  string $name 
     * @return bool
     */
    public function hasValidator($name)
    {
        return (false !== $this->_getValidatorIdentifier($name));
    }

    /**
     * Retrieve individual validator
     * 
     * @param  string $name 
     * @return Zend_Validate_Interface|null
     */
    public function getValidator($name)
    {
        if (false === ($identifier = $this->_getValidatorIdentifier($name))) {
            return null;
        }
        return $this->_validators[$identifier];
    }

    /**
     * Returns all set validators
     *
     * @param  string|array $files (Optional) Returns the validator for this files
     * @return null|array List of set validators
     * @throws Zend_File_Transfer_Exception When file not found
     */
    public function getValidators($files = null)
    {
        if ($files === null) {
            return $this->_validators;
        }

        if (!is_array($files)) {
            $files = array($files);
        }

        $validators = array();
        foreach ($files as $file) {
            if (!isset($this->_files[$file])) {
                require_once 'Zend/File/Transfer/Exception.php';
                throw new Zend_File_Transfer_Exception('Unknown file');
            }
            $validators += $this->_files[$file]['validators'];
        }
        $validators = array_unique($validators);

        $result = array();
        foreach ($validators as $validator) {
            $result[$validator] = $this->_validators[$validator];
        }
        return $result;
    }

    /**
     * Remove an individual validator 
     * 
     * @param  string $name 
     * @return Zend_File_Transfer_Adapter_Abstract
     */
    public function removeValidator($name)
    {
        if (false === ($key = $this->_getValidatorIdentifier($name))) {
            return $this;
        }

        unset($this->_validators[$key]);
        foreach (array_keys($this->_files) as $file) {
            if (!$index = array_search($key, $this->_files[$file]['validators'])) {
                continue;
            }
            unset($this->_files[$file]['validators'][$index]);
        }

        $this->_validated = false;
        return $this;
    }

    /**
     * Remove all validators
     * 
     * @return Zend_File_Transfer_Adapter_Abstract
     */
    public function clearValidators()
    {
        $this->_validators = array();
        foreach (array_keys($this->_files) as $file) {
            $this->_files[$file]['validators'] = array();
        }

        $this->_validated = false;
        return $this;
    }

    /**
     * Sets Options for adapters
     *
     * @param array $options
     */
    public function setOptions($options = array()) {
        if (is_array($options)) {
            foreach ($options as $name => $value) {
                if (array_key_exists($name, $this->_options)) {
                    $this->_options[$name] = (boolean) $value;
                } else {
                    require_once 'Zend/File/Transfer/Exception.php';
                    throw new Zend_File_Transfer_Exception("Unknown option: $name = $value");
                }
            }
        }

        return $this;
    }

    /**
     * Returns set options for adapters
     *
     * @param array $options
     */
    public function getOptions() {
        return $this->_options;
    }

    /**
     * Checks if the files are valid
     *
     * @param  string|array $files (Optional) Files to check
     * @return boolean True if all checks are valid
     */
    public function isValid($files = null)
    {
        $check           = $this->_getFiles($files);
        $translator      = $this->getTranslator();
        $this->_messages = array();
        $break           = false;
        foreach ($check as $content) {
            $fileerrors  = array();
            if (array_key_exists('validators', $content)) {
                foreach ($content['validators'] as $class) {
                    $validator = $this->_validators[$class];
                    if (method_exists($validator, 'setTranslator')) {
                        $validator->setTranslator($translator);
                    }

                    if (!$validator->isValid($content['tmp_name'], $content)) {
                        $fileerrors += $validator->getMessages();
                    }

                    if ($this->_options['ignoreNoFile'] and (isset($fileerrors['fileUploadErrorNoFile']))) {
                        unset($fileerrors['fileUploadErrorNoFile']);
                        break;
                    }

                    if (($class === 'Zend_Validate_File_Upload') and (count($fileerrors) > 0)) {
                        break;
                    }

                    if (($this->_break[$class]) and (count($fileerrors) > 0)) {
                        $break = true;
                        break;
                    }
                }
            }

            $this->_messages += $fileerrors;
            if ($break) {
                break;
            }
        }

        if (count($this->_messages) > 0) {
            $this->_validated = false;
            return false;
        }

        $this->_validated = true;
        return true;
    }

    /**
     * Returns found validation messages
     *
     * @return array
     */
    public function getMessages()
    {
        return $this->_messages;
    }

    /**
     * Retrieve error codes
     * 
     * @return array
     */
    public function getErrors()
    {
        return array_keys($this->_messages);
    }

    /**
     * Are there errors registered?
     * 
     * @return boolean
     */
    public function hasErrors()
    {
        return (!empty($this->_messages));
    }

    /**
     * Adds a new filter for this class
     *
     * @param  string|array $filter Type of filter to add
     * @param  string|array $options   Options to set for the filter
     * @param  string|array $files     Files to limit this filter to
     * @return Zend_File_Transfer_Adapter
     */
    public function addFilter($filter, $options = null, $files = null)
    {
        if ($filter instanceof Zend_Filter_Interface) {
            $class = get_class($filter);
        } elseif (is_string($filter)) {
            $class  = $this->getPluginLoader(self::FILTER)->load($filter);
            $filter = new $class($options);
        } else {
            require_once 'Zend/File/Transfer/Exception.php';
            throw new Zend_File_Transfer_Exception('Invalid filter specified');
        }

        $this->_filters[$class] = $filter;

        if ($files === null) {
            $files = array_keys($this->_files);
        } else {
            if (!is_array($files)) {
                $files = array($files);
            }
        }

        foreach ($files as $key => $file) {
            if (!is_string($file)) {
                if (is_array($file) && !is_numeric($key)) {
                    $file = $key;
                } else {
                    continue;
                }
            }

            if (!array_key_exists($file, $this->_files)) {
                continue;
            }

            $this->_files[$file]['filters'][] = $class;
        }
        
        return $this;
    }

    /**
     * Add Multiple filters at once
     * 
     * @param  array $filters 
     * @param  string|array $files 
     * @return Zend_File_Transfer_Adapter_Abstract
     */
    public function addFilters(array $filters, $files = null)
    {
        foreach ($filters as $key => $spec) {
            if ($spec instanceof Zend_Filter_Interface) {
                $this->addFilter($spec, null, $files);
                continue;
            }

            if (is_string($key)) {
                $this->addFilter($key, $spec, $files);
                continue;
            }

            if (is_int($key)) {
                if (is_string($spec)) {
                    $this->addFilter($spec, null, $files);
                    continue;
                }

                if (is_array($spec)) {
                    if (!array_key_exists('filter', $spec)) {
                        continue;
                    }

                    $filter = $spec['filter'];
                    unset($spec['filter']);
                    $this->addFilter($filter, $spec, $files);
                    continue;
                }

                continue;
            }
        }

        return $this;
    }

    /**
     * Sets a filter for the class, erasing all previous set
     *
     * @param  string|array $filter Filter to set
     * @param  string|array $files     Files to limit this filter to
     * @return Zend_File_Transfer_Adapter
     */
    public function setFilters(array $filters, $files = null)
    {
        $this->clearFilters();
        return $this->addFilters($filters, $files);
    }

    /**
     * Determine if a given filter has already been registered
     * 
     * @param  string $name 
     * @return bool
     */
    public function hasFilter($name)
    {
        return (false !== $this->_getFilterIdentifier($name));
    }

    /**
     * Retrieve individual filter
     * 
     * @param  string $name 
     * @return Zend_Filter_Interface|null
     */
    public function getFilter($name)
    {
        if (false === ($identifier = $this->_getFilterIdentifier($name))) {
            return null;
        }
        return $this->_filters[$identifier];
    }

    /**
     * Returns all set filters
     *
     * @param  string|array $files (Optional) Returns the filter for this files
     * @return null|array List of set filters
     * @throws Zend_File_Transfer_Exception When file not found
     */
    public function getFilters($files = null)
    {
        if ($files === null) {
            return $this->_filters;
        }

        if (!is_array($files)) {
            $files = array($files);
        }

        $filters = array();
        foreach ($files as $file) {
            if (!isset($this->_files[$file])) {
                require_once 'Zend/File/Transfer/Exception.php';
                throw new Zend_File_Transfer_Exception('Unknown file');
            }
            $filters += $this->_files[$file]['filters'];
        }
        $filters = array_unique($filters);

        foreach ($filters as $filter) {
            $result[] = $this->_filters[$filter];
        }
        return $result;
    }

    /**
     * Remove an individual filter 
     * 
     * @param  string $name 
     * @return Zend_File_Transfer_Adapter_Abstract
     */
    public function removeFilter($name)
    {
        if (false === ($key = $this->_getFilterIdentifier($name))) {
            return $this;
        }

        unset($this->_filters[$key]);
        foreach (array_keys($this->_files) as $file) {
            if (!$index = array_search($key, $this->_files[$file]['filters'])) {
                continue;
            }
            unset($this->_files[$file]['filters'][$index]);
        }
        return $this;
    }

    /**
     * Remove all filters
     * 
     * @return Zend_File_Transfer_Adapter_Abstract
     */
    public function clearFilters()
    {
        $this->_filters = array();
        foreach (array_keys($this->_files) as $file) {
            $this->_files[$file]['filters'] = array();
        }
        return $this;
    }

    /**
     * Returns all set files
     *
     * @return array List of set files
     * @throws Zend_File_Transfer_Exception Not implemented
     */
    public function getFile()
    {
        require_once 'Zend/File/Transfer/Exception.php';
        throw new Zend_File_Transfer_Exception('Method not implemented');
    }

    /**
     * Retrieve filename of transferred file
     *
     * Returns final target destination of transferred file.
     * 
     * @param  string $file 
     * @return string
     */
    public function getFileName($file)
    {
        $file = (string) $file;
        if (!array_key_exists($file, $this->_files)) {
             return null;
        }

        $directory = $this->getDestination($file);
        return $directory . DIRECTORY_SEPARATOR . $this->_files[$file]['name'];
    }

    /**
     * Retrieve additional internal file informations for files
     * 
     * @param  string $file (Optional) File to get informations for
     * @return array
     */
    public function getFileInfo($file = null)
    {
        return $this->_getFiles($file);
    }

    /**
     * Adds one or more files
     *
     * @param  string|array $file      File to add
     * @param  string|array $validator Validators to use for this file, must be set before
     * @param  string|array $filter    Filters to use for this file, must be set before
     * @return Zend_File_Transfer_Adapter_Abstract
     * @throws Zend_File_Transfer_Exception Not implemented
     */
    public function addFile($file, $validator = null, $filter = null)
    {
        require_once 'Zend/File/Transfer/Exception.php';
        throw new Zend_File_Transfer_Exception('Method not implemented');
    }

    /**
     * Returns all set types
     *
     * @return array List of set types
     * @throws Zend_File_Transfer_Exception Not implemented
     */
    public function getType()
    {
        require_once 'Zend/File/Transfer/Exception.php';
        throw new Zend_File_Transfer_Exception('Method not implemented');
    }

    /**
     * Adds one or more type of files
     *
     * @param  string|array $type Type of files to add
     * @param  string|array $validator Validators to use for this file, must be set before
     * @param  string|array $filter    Filters to use for this file, must be set before
     * @return Zend_File_Transfer_Adapter_Abstract
     * @throws Zend_File_Transfer_Exception Not implemented
     */
    public function addType($type, $validator = null, $filter = null)
    {
        require_once 'Zend/File/Transfer/Exception.php';
        throw new Zend_File_Transfer_Exception('Method not implemented');
    }

    /**
     * Sets a new destination for the given files
     *
     * @deprecated Will be changed to be a filter!!!
     * @param  string       $destination New destination directory
     * @param  string|array $files       Files to set the new destination for
     * @return Zend_File_Transfer_Abstract
     * @throws Zend_File_Transfer_Exception when the given destination is not a directory or does not exist
     */
    public function setDestination($destination, $files = null)
    {
        $destination = rtrim($destination, "/\\");
        if (!is_dir($destination)) {
            require_once 'Zend/File/Transfer/Exception.php';
            throw new Zend_File_Transfer_Exception('The given destination is no directory or does not exist');
        }
        
        if ($files === null) {
            foreach ($this->_files as $file => $content) {
                $this->_files[$file]['destination'] = $destination;
            }
        } else {
            if (!is_array($files)) {
                $files = array($files);
            }

            foreach ($files as $file) {
                $this->_files[$file]['destination'] = $destination;
            }
        }

        return $this;
    }

    /**
     * Retrieve destination directory value
     * 
     * @param  null|string|array $files 
     * @return null|string|array
     */
    public function getDestination($files = null)
    {
        if ((null === $files) || is_array($files)) {
            $destinations = array();
            if (!is_array($files)) {
                $files = $this->_files;
            } else {
                $files = array_flip($files);
                $files = array_intersect_assoc($files, $this->_files);
            }
            foreach ($files as $file => $content) {
                if (array_key_exists('destination', $content)) {
                    $destinations[$file] = $content['destination'];
                } else {
                    $tmpdir = $this->_getTmpDir();
                    $this->setDestination($tmpdir, $file);
                    $destinations[$file] = $tmpdir;
                }
            }
            return $destinations;
        }

        if (!is_string($files)) {
            require_once 'Zend/File/Transfer/Exception.php';
            throw new Zend_File_Transfer_Exception('Invalid file value passed to getDestination()');
        }

        if (!array_key_exists($files, $this->_files)) {
            require_once 'Zend/File/Transfer/Exception.php';
            throw new Zend_File_Transfer_Exception(sprintf('Unknown file "%s" passed to getDestination()', $files));
        }

        if (!array_key_exists('destination', $this->_files[$files])) {
            return $this->_getTmpDir();
        }

        return $this->_files[$files]['destination'];
    }

    /**
     * Set translator object for localization
     *
     * @param  Zend_Translate|null $translator 
     * @return Zend_File_Transfer_Abstract
     */
    public function setTranslator($translator = null)
    {
        if (null === $translator) {
            $this->_translator = null;
        } elseif ($translator instanceof Zend_Translate_Adapter) {
            $this->_translator = $translator;
        } elseif ($translator instanceof Zend_Translate) {
            $this->_translator = $translator->getAdapter();
        } else {
            require_once 'Zend/File/Transfer/Exception.php';
            throw new Zend_File_Transfer_Exception('Invalid translator specified');
        }

        return $this;
    }

    /**
     * Retrieve localization translator object
     * 
     * @return Zend_Translate_Adapter|null
     */
    public function getTranslator()
    {
        if ($this->translatorIsDisabled()) {
            return null;
        }

        return $this->_translator;
    }

    /**
     * Indicate whether or not translation should be disabled
     * 
     * @param  bool $flag 
     * @return Zend_File_Transfer_Abstract
     */
    public function setDisableTranslator($flag)
    {
        $this->_translatorDisabled = (bool) $flag;
        return $this;
    }

    /**
     * Is translation disabled?
     * 
     * @return bool
     */
    public function translatorIsDisabled()
    {
        return $this->_translatorDisabled;
    }

    /**
     * Internal function to filter all given files
     *
     * @param  string|array $files (Optional) Files to check
     * @return boolean False on error
     */
    protected function _filter($files = null)
    {
        $check           = $this->_getFiles($files);
        foreach ($check as $name => $content) {
            if (array_key_exists('filters', $content)) {
                foreach ($content['filters'] as $class) {
                    $filter = $this->_filters[$class];
                    try {
                        $result = $filter->filter($this->getFileName($name));

                        $this->_files[$name]['destination'] = dirname($result);
                        $this->_files[$name]['name']        = basename($result);
                    } catch (Zend_Filter_Exception $e) {
                        $this->_messages += array($e->getMessage());
                    }
                }
            }
        }

        if (count($this->_messages) > 0) {
            return false;
        }

        return true;
    }

    /**
     * Determine system TMP directory
     * 
     * @return string
     * @throws Zend_File_Transfer_Exception if unable to determine directory
     */
    protected function _getTmpDir()
    {
        if (null === $this->_tmpDir) {
            if (function_exists('sys_get_temp_dir')) {
                $tmpdir = sys_get_temp_dir();
            } elseif (!empty($_ENV['TMP'])) {
                $tmpdir = realpath($_ENV['TMP']);
            } elseif (!empty($_ENV['TMPDIR'])) {
                $tmpdir = realpath($_ENV['TMPDIR']);
            } else if (!empty($_ENV['TEMP'])) {
                $tmpdir = realpath($_ENV['TEMP']);
            } else {
                // Attemp to detect by creating a temporary file
                $tempFile = tempnam(md5(uniqid(rand(), TRUE)), '');
                if ($tempFile) {
                    $tmpdir = realpath(dirname($tempFile));
                    unlink($tempFile);
                } else {
                    require_once 'Zend/File/Transfer/Exception.php';
                    throw new Zend_File_Transfer_Exception('Could not determine temp directory');
                }
            }
            $this->_tmpDir = rtrim($tmpdir, "/\\");
        }
        return $this->_tmpDir;
    }

    /**
     * Returns found files based on internal file array and given files
     *
     * @param  string|array $files (Optional) Files to return
     * @return array Found files
     * @throws Zend_File_Transfer_Exception On false filename
     */
    protected function _getFiles($files)
    {
        $check = null;

        if (is_string($files)) {
            $files = array($files);
        }

        if (is_array($files)) {
            foreach ($files as $find) {
                $found = null;
                foreach ($this->_files as $file => $content) {
                    if ($content['name'] === $find) {
                        $found = $file;
                        break;
                    }

                    if ($file === $find) {
                        $found = $file;
                        break;
                    }
                }

                if ($found === null) {
                    require_once 'Zend/File/Transfer/Exception.php';
                    throw new Zend_File_Transfer_Exception(sprintf('"%s" not found by file transfer adapter', $file));
                }

                $check[$found] = $this->_files[$found];
            }
        }

        if ($files === null) {
            $check = $this->_files;
        }

        return $check;
    }

    /**
     * Retrieve internal identifier for a named validator
     * 
     * @param  string $name 
     * @return string
     */
    protected function _getValidatorIdentifier($name)
    {
        if (array_key_exists($name, $this->_validators)) {
            return $name;
        }

        foreach (array_keys($this->_validators) as $test) {
            if (preg_match('/' . preg_quote($name) . '$/i', $test)) {
                return $test;
            }
        }

        return false;
    }

    /**
     * Retrieve internal identifier for a named filter
     * 
     * @param  string $name 
     * @return string
     */
    protected function _getFilterIdentifier($name)
    {
        if (array_key_exists($name, $this->_filters)) {
            return $name;
        }

        foreach (array_keys($this->_filters) as $test) {
            if (preg_match('/' . preg_quote($name) . '$/i', $test)) {
                return $test;
            }
        }

        return false;
    }
}
