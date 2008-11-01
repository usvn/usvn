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
 * @package    Zend_Loader
 * @subpackage PluginLoader
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/** Zend_Loader_PluginLoader_Interface */
require_once 'Zend/Loader/PluginLoader/Interface.php';

/** Zend_Loader */
require_once 'Zend/Loader.php';

/**
 * Generic plugin class loader
 *
 * @category   Zend
 * @package    Zend_Loader
 * @subpackage PluginLoader
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Loader_PluginLoader implements Zend_Loader_PluginLoader_Interface
{
    /**
     * Static registry property
     *
     * @var array
     */
    static protected $_staticPrefixToPaths = array();

    /**
     * Instance registry property
     *
     * @var array
     */
    protected $_prefixToPaths = array();

    /**
     * Statically loaded plugins
     *
     * @var array
     */
    static protected $_staticLoadedPlugins = array();

    /**
     * Statically loaded plugin path mappings
     *
     * @var array
     */
    static protected $_staticLoadedPluginPaths = array();

    /**
     * Instance loaded plugins
     *
     * @var array
     */
    protected $_loadedPlugins = array();

    /**
     * Instance loaded plugin paths
     *
     * @var array
     */
    protected $_loadedPluginPaths = array();

    /**
     * Whether to use a statically named registry for loading plugins
     *
     * @var string|null
     */
    protected $_useStaticRegistry = null;

    /**
     * Constructor
     *
     * @param array $prefixToPaths
     * @param string $staticRegistryName OPTIONAL
     */
    public function __construct(Array $prefixToPaths = array(), $staticRegistryName = null)
    {
        if (is_string($staticRegistryName) && !empty($staticRegistryName)) {
            $this->_useStaticRegistry = $staticRegistryName;
            self::$_staticPrefixToPaths[$staticRegistryName] = array();
            self::$_staticLoadedPlugins[$staticRegistryName] = array();
        }

        foreach ($prefixToPaths as $prefix => $path) {
            $this->addPrefixPath($prefix, $path);
        }
    }

    /**
     * Format prefix for internal use
     *
     * @param  string $prefix
     * @return string
     */
    protected function _formatPrefix($prefix)
    {
        return rtrim($prefix, '_') . '_';
    }

    /**
     * Add prefixed paths to the registry of paths
     *
     * @param string $prefix
     * @param string $path
     * @return Zend_Loader_PluginLoader
     */
    public function addPrefixPath($prefix, $path)
    {
        if (!is_string($prefix) || !is_string($path)) {
            require_once 'Zend/Loader/PluginLoader/Exception.php';
            throw new Zend_Loader_PluginLoader_Exception('Zend_Loader_PluginLoader::addPrefixPath() method only takes strings for prefix and path.');
        }

        $prefix = $this->_formatPrefix($prefix);
        $path   = rtrim($path, '/\\') . '/';

        if ($this->_useStaticRegistry) {
            if (!array_key_exists($prefix, self::$_staticPrefixToPaths[$this->_useStaticRegistry])) {
                self::$_staticPrefixToPaths[$this->_useStaticRegistry][$prefix] = array($path);
            } elseif (!in_array($path, self::$_staticPrefixToPaths[$this->_useStaticRegistry][$prefix])) {
                self::$_staticPrefixToPaths[$this->_useStaticRegistry][$prefix][] = $path;
            }
        } else {
            if (!array_key_exists($prefix, $this->_prefixToPaths)) {
                $this->_prefixToPaths[$prefix] = array($path);
            } elseif (!in_array($path, $this->_prefixToPaths[$prefix])) {
                $this->_prefixToPaths[$prefix][] = $path;
            }
        }

        return $this;
    }

    /**
     * Get path stack
     *
     * @param  string $prefix
     * @return false|array False if prefix does not exist, array otherwise
     */
    public function getPaths($prefix = null)
    {
        if ((null !== $prefix) && is_string($prefix)) {
            $prefix = $this->_formatPrefix($prefix);
            if ($this->_useStaticRegistry) {
                if (isset(self::$_staticPrefixToPaths[$this->_useStaticRegistry][$prefix])) {
                    return self::$_staticPrefixToPaths[$this->_useStaticRegistry][$prefix];
                }

                return false;
            }

            if (isset($this->_prefixToPaths[$prefix])) {
                return $this->_prefixToPaths[$prefix];
            }

            return false;
        }

        if ($this->_useStaticRegistry) {
            return self::$_staticPrefixToPaths[$this->_useStaticRegistry];
        }

        return $this->_prefixToPaths;
    }

    /**
     * Clear path stack
     *
     * @param  string $prefix
     * @return bool False only if $prefix does not exist
     */
    public function clearPaths($prefix = null)
    {
        if ((null !== $prefix) && is_string($prefix)) {
            $prefix = $this->_formatPrefix($prefix);
            if ($this->_useStaticRegistry) {
                if (isset(self::$_staticPrefixToPaths[$this->_useStaticRegistry][$prefix])) {
                    unset(self::$_staticPrefixToPaths[$this->_useStaticRegistry][$prefix]);
                    return true;
                }

                return false;
            }

            if (isset($this->_prefixToPaths[$prefix])) {
                unset($this->_prefixToPaths[$prefix]);
                return true;
            }

            return false;
        }

        if ($this->_useStaticRegistry) {
            self::$_staticPrefixToPaths[$this->_useStaticRegistry] = array();
        } else {
            $this->_prefixToPaths = array();
        }

        return true;
    }

    /**
     * Remove a prefix (or prefixed-path) from the registry
     *
     * @param string $prefix
     * @param string $path OPTIONAL
     * @return Zend_Loader_PluginLoader
     */
    public function removePrefixPath($prefix, $path = null)
    {
        $prefix = $this->_formatPrefix($prefix);
        if ($this->_useStaticRegistry) {
            $registry =& self::$_staticPrefixToPaths[$this->_useStaticRegistry];
        } else {
            $registry =& $this->_prefixToPaths;
        }

        if (!isset($registry[$prefix])) {
            require_once 'Zend/Loader/PluginLoader/Exception.php';
            throw new Zend_Loader_PluginLoader_Exception('Prefix ' . $prefix . ' was not found in the PluginLoader.');
        }

        if ($path != null) {
            $pos = array_search($path, $registry[$prefix]);
            if ($pos === null) {
                require_once 'Zend/Loader/PluginLoader/Exception.php';
                throw new Zend_Loader_PluginLoader_Exception('Prefix ' . $prefix . ' / Path ' . $path . ' was not found in the PluginLoader.');
            }
            unset($registry[$prefix][$pos]);
        } else {
            unset($registry[$prefix]);
        }

        return $this;
    }

    /**
     * Normalize plugin name
     *
     * @param  string $name
     * @return string
     */
    protected function _formatName($name)
    {
        return ucfirst((string) $name);
    }

    /**
     * Whether or not a Plugin by a specific name is loaded
     *
     * @param string $name
     * @return Zend_Loader_PluginLoader
     */
    public function isLoaded($name)
    {
        $name = $this->_formatName($name);
        if ($this->_useStaticRegistry) {
            return isset(self::$_staticLoadedPlugins[$this->_useStaticRegistry][$name]);
        }

        return isset($this->_loadedPlugins[$name]);
    }

    /**
     * Return full class name for a named plugin
     *
     * @param string $name
     * @return string|false False if class not found, class name otherwise
     */
    public function getClassName($name)
    {
        $name = $this->_formatName($name);
        if ($this->_useStaticRegistry 
            && isset(self::$_staticLoadedPlugins[$this->_useStaticRegistry][$name])
        ) {
            return self::$_staticLoadedPlugins[$this->_useStaticRegistry][$name];
        } elseif (isset($this->_loadedPlugins[$name])) {
            return $this->_loadedPlugins[$name];
        }

        return false;
    }

    /**
     * Get path to plugin class
     * 
     * @param  mixed $name 
     * @return string|false False if not found
     */
    public function getClassPath($name)
    {
        $name = $this->_formatName($name);
        if ($this->_useStaticRegistry 
            && isset(self::$_staticLoadedPluginPaths[$this->_useStaticRegistry][$name])
        ) {
            return self::$_staticLoadedPluginPaths[$this->_useStaticRegistry][$name];
        }
        
        if (isset($this->_loadedPluginPaths[$name])) {
            return $this->_loadedPluginPaths[$name];
        }

        if ($this->_useStaticRegistry
            && isset(self::$_staticLoadedPlugins[$this->_useStaticRegistry][$name])
        ) {
            $class = self::$_staticLoadedPlugins[$this->_useStaticRegistry][$name];
            $r = new ReflectionClass($class);
            self::$_staticLoadedPluginPaths[$this->_useStaticRegistry][$name] = $r->getFileName();
            return self::$_staticLoadedPluginPaths[$this->_useStaticRegistry][$name];
        }

        if (isset($this->_loadedPlugins[$name])) {
            $class = $this->_loadedPlugins[$name];
            $r = new ReflectionClass($class);
            $this->_loadedPluginPaths[$name] = $r->getFileName();
            return $this->_loadedPluginPaths[$name];
        }

        return false;
    }

    /**
     * Load a plugin via the name provided
     *
     * @param  string $name
     * @return string Class name of loaded class
     * @throws Zend_Loader_Exception if class not found
     */
    public function load($name)
    {
        $name = $this->_formatName($name);
        if ($this->_useStaticRegistry) {
            $registry = self::$_staticPrefixToPaths[$this->_useStaticRegistry];
        } else {
            $registry = $this->_prefixToPaths;
        }

        if ($this->isLoaded($name)) {
            return $this->getClassName($name);
        }

        $found          = false;
        $classFile      = str_replace('_', DIRECTORY_SEPARATOR, $name) . '.php';
        $registry       = array_reverse($registry, true);
        $incPathsMaster = explode(PATH_SEPARATOR, get_include_path());
        foreach ($registry as $prefix => $paths) {
            $incPaths  = $incPathsMaster;
            $className = $prefix . $name;

            if (class_exists($className, false)) {
                $found = true;
                break;
            }

            $paths = array_reverse($paths, true);
            foreach ($paths as $key => $path) {
                if (preg_match('/^[a-z0-9_](?!(:\/))(?!:' . preg_quote('\\') . ')/i', $path)) {
                    foreach ($incPaths as $index => $incPath) {
                        $incPaths[$index] .= DIRECTORY_SEPARATOR . $path;
                    }
                    unset($paths[$key]);
                }
            }
            $paths = array_merge($paths, $incPaths);
            if (Zend_Loader::loadFile($classFile, $paths, true)) {
                if (class_exists($className, false)) {
                    $found = true;
                    break;
                }
            }
        }

        if ($found) {
            if ($this->_useStaticRegistry) {
                self::$_staticLoadedPlugins[$this->_useStaticRegistry][$name] = $className;
            } else {
                $this->_loadedPlugins[$name] = $className;
            }
            return $className;
        }

        require_once 'Zend/Loader/PluginLoader/Exception.php';
        $message = 'Plugin by name ' . $name . ' was not found in the registry; paths searched: '
                 . var_export($registry, 1) . "\n"
                 . 'Original include_path: ' . get_include_path();
        throw new Zend_Loader_PluginLoader_Exception($message);
    }
}
