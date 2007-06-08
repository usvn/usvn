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
 * Zend_Gdata_Feed
 */
require_once 'Zend/Gdata/Feed.php';

/**
 * Zend_Gdata_Http_Client
 */
require_once 'Zend/Http/Client.php';

/**
 * Zend_Version
 */
require_once 'Zend/Version.php';

/**
 * Provides Atom Publishing Protocol (APP) functionality.  This class and all
 * other components of Zend_Gdata_App are designed to work independently from
 * other Zend_Gdata components in order to interact with generic APP services.
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Gdata_App
{

    /**
     * Client object used to communicate
     *
     * @var Zend_Http_Client
     */
    protected $_httpClient;

    /**
     * Client object used to communicate in static context
     *
     * @var Zend_Http_Client
     */
    protected static $_staticHttpClient = null;

    /**
     * Override HTTP PUT and DELETE request methods?
     *
     * @var boolean
     */
    protected static $_httpMethodOverride = false;

    /**
     * Default URI to which to POST.
     *
     * @var string
     */
    protected $_defaultPostUri = null;

    /**
     * Packages to search for classes when using magic __call method, in order.
     *
     * @var array 
     */
    protected $_registeredPackages = array(
            'Zend_Gdata_App_Extension',
            'Zend_Gdata_App');

    /**
     * Create Gdata object
     *
     * @param Zend_Http_Client $client
     */
    public function __construct($client = null)
    {
        $this->setHttpClient($client);
    }

    /**
     * Adds a Zend Framework package to the $_registeredPackages array.
     * This array is searched when using the magic __call method below
     * to instantiante new objects.
     *
     * @param string $name The name of the package (eg Zend_Gdata_App)
     * @return void
     */
    public function registerPackage($name) 
    {
        array_unshift($this->_registeredPackages, $name);
    }

    /**
     * Retreive feed object
     *
     * @param string $uri The uri from which to retrieve the feed
     * @param string $className The class which is used as the return type
     * @return Zend_Gdata_App_Feed
     */
    public function getFeed($uri, $className='Zend_Gdata_App_Feed')
    {
        $this->_httpClient->resetParameters();
        return $this->import($uri, $this->_httpClient, $className);
    }

    /**
     * Retreive entry object
     *
     * @param string $uri
     * @param string $className The class which is used as the return type
     * @return Zend_Gdata_App_Entry
     */
    public function getEntry($uri, $className='Zend_Gdata_App_Entry')
    {
        $this->_httpClient->resetParameters();
        return $this->import($uri, $this->_httpClient, $className);
    }

    /**

    /**
     * Get the Zend_Http_Client object used for communication 
     *
     * @return Zend_Http_Client
     */
    public function getHttpClient()
    {
        return $this->_httpClient;
    }

    /**
     * Set the Zend_Http_Client object used for communication 
     *
     * @param Zend_Http_Client $client The client to use for communication
     * @throws Zend_Gdata_App_HttpException
     * @return Zend_Gdata_App Provides a fluent interface
     */
    public function setHttpClient($client)
    {
        if ($client == null) {
            $client = new Zend_Http_Client();
        }
        if (!$client instanceof Zend_Http_Client) {
            require_once 'Zend/Gdata/App/HttpException.php';
            throw new Zend_Gdata_App_HttpException('Argument is not an instance of Zend_Http_Client.');
        }
        $useragent = 'Zend_Framework_Gdata/' . Zend_Version::VERSION;
        $client->setConfig(array(
            'strictredirects' => true,
            'useragent' => $useragent
            )
        );
        $this->_httpClient = $client;
        Zend_Gdata::setStaticHttpClient($client); 
        return $this;
    }


    /**
     * Set the static HTTP client instance
     *
     * Sets the static HTTP client object to use for retrieving the feed.
     *
     * @param  Zend_Http_Client $httpClient
     * @return void
     */
    public static function setStaticHttpClient(Zend_Http_Client $httpClient)
    {
        self::$_staticHttpClient = $httpClient;
    }


    /**
     * Gets the HTTP client object. If none is set, a new Zend_Http_Client will be used.
     *
     * @return Zend_Http_Client
     */
    public static function getStaticHttpClient()
    {
        if (!self::$_staticHttpClient instanceof Zend_Http_Client) {
            $client = new Zend_Http_Client();
            $useragent = 'Zend_Framework_Gdata/' . Zend_Version::VERSION;
            $client->setConfig(array(
                'strictredirects' => true,
                'useragent' => $useragent
                )
            );
            self::$_staticHttpClient = $client;
        }
        return self::$_staticHttpClient;
    }

    /**
     * Toggle using POST instead of PUT and DELETE HTTP methods
     *
     * Some feed implementations do not accept PUT and DELETE HTTP
     * methods, or they can't be used because of proxies or other
     * measures. This allows turning on using POST where PUT and
     * DELETE would normally be used; in addition, an
     * X-Method-Override header will be sent with a value of PUT or
     * DELETE as appropriate.
     *
     * @param  boolean $override Whether to override PUT and DELETE with POST.
     * @return void
     */
    public static function setHttpMethodOverride($override = true)
    {
        self::$_httpMethodOverride = $override;
    }

    /**
     * Get the HTTP override state
     *
     * @return boolean
     */
    public static function getHttpMethodOverride()
    {
        return self::$_httpMethodOverride;
    }

    /**
     * Imports a feed located at $uri.
     *
     * @param  string $uri
     * @param  Zend_Http_Client $client The client used for communication
     * @param  string $className The class which is used as the return type
     * @throws Zend_Gdata_App_Exception
     * @return Zend_Gdata_App_Feed
     */
    public static function import($uri, $client = null, $className='Zend_Gdata_App_Feed')
    {
        $client->setUri($uri);
        $response = $client->request('GET');
        if ($response->getStatus() !== 200) {
            require_once 'Zend/Gdata/App/HttpException.php';
            $exception = new Zend_Gdata_App_HttpException('Expected response code 200, got ' . $response->getStatus());
            $exception->setResponse($response);
            throw $exception;
        }
        $feedContent = $response->getBody();
        $feed = self::importString($feedContent, $className);
        if ($client != null) {
            $feed->setHttpClient($client);
        } else {
            $feed->setHttpClient(self::getStaticHttpClient());
        }
        return $feed;
    }


    /**
     * Imports a feed represented by $string.
     *
     * @param  string $string
     * @param  string $className The class which is used as the return type
     * @throws Zend_Gdata_App_Exception
     * @return Zend_Gdata_App_Feed
     */
    public static function importString($string, $className='Zend_Gdata_App_Feed')
    {
        // Load the feed as an XML DOMDocument object
        @ini_set('track_errors', 1);
        $doc = new DOMDocument();
        $success = @$doc->loadXML($string);
        @ini_restore('track_errors');

        if (!$success) {
            require_once 'Zend/Gdata/App/Exception.php';
            throw new Zend_Gdata_App_Exception("DOMDocument cannot parse XML: $php_errormsg");
        }
        $feed = new $className($string);
        $feed->setHttpClient(self::getstaticHttpClient());
        return $feed;
    }


    /**
     * Imports a feed from a file located at $filename.
     *
     * @param  string $filename
     * @param  string $className The class which is used as the return type
     * @throws Zend_Gdata_App_Exception
     * @return Zend_Gdata_Feed
     */
    public static function importFile($filename, $className='Zend_Gdata_App_Feed')
    {
        @ini_set('track_errors', 1);
        $feed = @file_get_contents($filename);
        @ini_restore('track_errors');
        if ($feed === false) {
            require_once 'Zend/Gdata/App/Exception.php';
            throw new Zend_Gdata_App_Exception("File could not be loaded: $php_errormsg");
        }
        return self::importString($feed, $className);
    }

    /**
     * POST data to Google with authorization headers set
     *
     * @param mixed $data The Zend_Gdata_App_Entry or XML to post
     * @param string $uri POST URI
     * @return Zend_Http_Response
     * @throws Zend_Gdata_App_Exception
     * @throws Zend_Gdata_App_HttpException
     * @throws Zend_Gdata_App_InvalidArgumentException
     */
    public function post($data, $uri= null)
    {
        require_once 'Zend/Http/Client/Exception.php';
        if (is_string($data)) {
            $rawData = $data;
        } elseif ($data instanceof Zend_Gdata_App_Entry) {
            $rawData = $data->saveXML();
        } else {
            require_once 'Zend/Gdata/App/InvalidArgumentException.php';
            throw new Zend_Gdata_App_InvalidArgumentException(
                    'You must specify the data to post as either a string or a child of Zend_Gdata_App_Entry');
        } 
        if ($uri == null) {
            $uri = $this->_defaultPostUri;
        }
        if ($uri == null) {
            require_once 'Zend/Gdata/App/InvalidArgumentException.php';
            throw new Zend_Gdata_App_InvalidArgumentException('You must specify an URI to which to post.');
        }
        $this->_httpClient->setUri($uri);
        $this->_httpClient->setConfig(array('maxredirects' => 0));
        $this->_httpClient->setRawData($rawData,'application/atom+xml');
        try {
            $response = $this->_httpClient->request('POST');
        } catch (Zend_Http_Client_Exception $e) {
            require_once 'Zend/Gdata/App/HttpException.php';
            throw new Zend_Gdata_App_HttpException($e->getMessage(), $e);
        }
        /**
         * set "S" cookie to avoid future redirects.
         */
        if($cookie = $response->getHeader('Set-cookie')) {
            list($cookieName, $cookieValue) = explode('=', $cookie, 2);
            $this->_httpClient->setCookie($cookieName, $cookieValue);
        }
        if ($response->isRedirect()) {
            /**
             * Re-POST with redirected URI.
             * This happens frequently.
             */
            $this->_httpClient->setUri($response->getHeader('Location'));
            $this->_httpClient->setRawData($rawData,'application/atom+xml');
            try {
                $response = $this->_httpClient->request('POST');
            } catch (Zend_Http_Client_Exception $e) {
                throw new Zend_Gdata_App_HttpException($e->getMessage(), $e);
            }
        }
        
        if (!$response->isSuccessful()) {
            require_once 'Zend/Gdata/App/HttpException.php';
            $exception = new Zend_Gdata_App_HttpException('Expected response code 200, got ' . $response->getStatus());
            $exception->setResponse($response);
            throw $exception;
        }
        return $response;
    }

    /**
     * Inserts an entry to a given URI and returns the response as a fully formed Entry.
     * @param mixed  $data The Zend_Gdata_App_Entry or XML to post
     * @param string $uri POST URI
     * @param string $className The class of entry to be returned.
     * @return Zend_Gdata_App_Entry The entry returned by the service after insertion.
     */
    public function insertEntry($data, $uri, $className='Zend_Gdata_App_Entry')
    {
        if (is_string($data)) {
            $rawData = $data;
        } elseif ($data instanceof Zend_Gdata_App_Entry) {
            $rawData = $data->saveXML();
        } else {
            require_once 'Zend/Gdata/App/InvalidArgumentException.php';
            throw new Zend_Gdata_App_InvalidArgumentException(
                    'You must specify the data to post as either a string or a child of Zend_Gdata_App_Entry');
        }
        $response = $this->post($rawData, $uri);
        
        $returnEntry = new $className($response->getBody());
        $returnEntry->setHttpClient(self::getstaticHttpClient());
        return $returnEntry;
    }

    /**
     * Delete an entry
     *
     * TODO Determine if App should call Entry to Delete or the opposite.  
     * Suspecect opposite would mkae more sense
     * 
     * @param string $data The Zend_Gdata_App_Entry or URL to delete
     * @throws Zend_Gdata_App_Exception
     */
    public function delete($data)
    {
        if (is_string($data)) {
            $uri = $data;
            $entry = $this->getEntry($uri);
        } elseif ($data instanceof Zend_Gdata_App_Entry) {
            $entry = $data; 
        } else {
            require_once 'Zend/Gdata/App/InvalidArgumentException.php';
            throw new Zend_Gdata_App_InvalidArgumentException(
                    'You must specify the data to post as either a string or a child of Zend_Gdata_App_Entry');
        } 
        $entry->delete();
        return true;
    }

    /**
     * Put an entry
     *
     * TODO Determine if App should call Entry to Update or the opposite.  
     * Suspecect opposite would mkae more sense.  Also, this possibly should
     * take an optional URL to override URL used in the entry, or if an
     * edit URI/ID is not present in the entry
     *
     * @param mixed $data Zend_Gdata_App_Entry or XML (w/ID and link rel='edit')
     * @return Zend_Gdata_App_Entry The entry returned from the server
     * @throws Zend_Gdata_App_Exception
     */
    public function put($data)
    {
        if (is_string($data)) {
            $entry = new Zend_Gdata_App_Entry($data);
            $entry->setHttpClient($this->_httpClient); 
            return $entry->save();
        } elseif ($data instanceof Zend_Gdata_App_Entry) {
            return $data->save();
        } else {
            require_once 'Zend/Gdata/App/InvalidArgumentException.php';
            throw new Zend_Gdata_App_InvalidArgumentException(
                    'You must specify the data to post as either a XML string or a child of Zend_Gdata_App_Entry');
        } 
    }

    /**
     * Provides a magic factory method to instantiate new objects with
     * shorter syntax than would otherwise be required by the Zend Framework
     * naming conventions.  For instance, to construct a new 
     * Zend_Gdata_Calendar_Extension_Color, a developer simply needs to do
     * $gCal->newColor().  For this magic constructor, packages are searched
     * in the same order as which they appear in the $_registeredPackages
     * array
     *
     * @param string $method The method name being called
     * @param array $args The arguments passed to the call
     * @throws Zend_Gdata_App_Exception
     */
    public function __call($method, $args) 
    {
        if (preg_match('/^new(\w+)/', $method, $matches)) {
            $class = $matches[1];
            $foundClassName = null;
            foreach ($this->_registeredPackages as $name) {
                 try {
                     Zend_Loader::loadClass("${name}_${class}");
                     $foundClassName = "${name}_${class}";
                     break;
                 } catch (Zend_Exception $e) {
                     // package wasn't here- continue searching
                 }
            }
            if ($foundClassName != null) {
                $reflectionObj = new ReflectionClass($foundClassName);
                return $reflectionObj->newInstanceArgs($args);
            } else {
                require_once 'Zend/Gdata/App/Exception.php';
                throw new Zend_Gdata_App_Exception(
                        "Unable to find '${class}' in registered packages");
            }
        } else {
            require_once 'Zend/Gdata/App/Exception.php';
            throw new Zend_Gdata_App_Exception("No such method ${method}");
        }
    }

}
