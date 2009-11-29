<?php

/* Defines */

/* ROOTS */
define('USVN_LIB_DIR',            USVN_BASE_DIR . '/library');
define('USVN_PUB_DIR',            USVN_BASE_DIR . '/public');
define('USVN_CONFIG_DIR',         USVN_BASE_DIR . '/config');

/* Libraries */
define('ZEND_DIRECTORY',          USVN_LIB_DIR . '/Zend');
define('USVN_DIRECTORY',          USVN_LIB_DIR . '/USVN');
define('USVN_ROUTES_CONFIG_FILE', APPLICATION_PATH . '/configs/routes.ini');

/* Application */
define('USVN_CONTROLLERS_DIR',    APPLICATION_PATH . '/controllers');
define('USVN_HELPERS_DIR',        APPLICATION_PATH . '/helpers');
define('USVN_VIEWS_DIR',          APPLICATION_PATH . '/views/scripts');
define('USVN_LAYOUTS_DIR',        APPLICATION_PATH . '/layouts');
define('USVN_MODEL_DIR',          APPLICATION_PATH . '/models');
define('USVN_MEDIAS_DIR',         USVN_PUB_DIR . '/medias/');
define('USVN_LOCALE_DIR',         APPLICATION_PATH . '/locale');

/* Config */
define('USVN_CONFIG_FILE',        USVN_CONFIG_DIR . '/config.ini');
define('USVN_CONFIG_SECTION',     'general');
define('USVN_CONFIG_VERSION',     '1.1.0');


/* Misc */
define('USVN_URL_SEP', ':');
define('USVN_DIRECTORY_SEPARATOR', '+');
error_reporting(E_ALL | E_STRICT);


$USVNLogArray = array();
function USVNLogObject($name, $value)
{
    if (APPLICATION_ENV == 'development')
    {
        global $USVNLogArray;
        $USVNLogArray[] = array('name' => $name, 'value' => $value);
    }
}

function T_($str)
{
	return USVN_Translation::_($str);
}

function h_($string)
{
	return htmlspecialchars($string);
}


class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected function _initAutoload()
    {
        $autoloader = new Zend_Application_Module_Autoloader(array(
            'namespace' => 'Default',
            'basePath'  => dirname(__FILE__),
        ));
        return $autoloader;
    }

    protected function _initDoctype()
    {
        $this->bootstrap('view');
        $view = $this->getResource('view');
        $view->doctype('XHTML1_STRICT');
    }

    public function _bootstrap($resource = null)
    {
        USVNLogObject('resource', $resource);
        parent::_bootstrap($resource);
    }

    public function run()
    {
        global $USVNLogArray;
        $autoloader = Zend_Loader_Autoloader::getInstance();
        $autoloader->registerNamespace('USVN_');
        $autoloader->setFallbackAutoloader(true);

        /* Config Loading or Installation */
        try
        {
            $config = new USVN_Config_Ini(USVN_CONFIG_FILE, USVN_CONFIG_SECTION);
            if (empty($config) && file_exists(USVN_CONFIG_FILE))
            {
                echo 'Config file unreadeable';
                exit(0);
            }
            if (!isset($config->version))
            {
                header('Location: install.php');
                exit(0);
            }
            if ($config->version != USVN_CONFIG_VERSION)
            {
                USVN_Update::runUpdate();
                $config = new USVN_Config_Ini(USVN_CONFIG_FILE, USVN_CONFIG_SECTION);
            }
        }
        catch (Exception $e)
        {
            header('Location: install.php');
            exit(0);
        }

        /* USVN Configuration */
        date_default_timezone_set($config->timezone);

        $locale = new Zend_Locale($config->translation->locale);
        Zend_Registry::set('Zend_Locale', $locale);

        USVN_ConsoleUtils::setLocale($config->system->locale);
        USVN_Translation::initTranslation($config->translation->locale, USVN_LOCALE_DIR);
        USVN_Template::initTemplate($config->template->name, USVN_MEDIAS_DIR);

        /* Zend Configuration */
        Zend_Registry::set('config', $config);
        Zend_Db_Table::setDefaultAdapter(Zend_Db::factory($config->database->adapterName, $config->database->options->toArray()));
        if (isset($config->database->prefix))
        {
    	    USVN_Db_Table::$prefix = $config->database->prefix;
        }

        $front = Zend_Controller_Front::getInstance();
        $front->setRequest(new USVN_Controller_Request_Http());
        $front->throwExceptions(true);
        $front->setBaseUrl($config->url->base);
        $front->getRouter()->addConfig(new Zend_Config_Ini(USVN_ROUTES_CONFIG_FILE, 'production'), 'routes');

        parent::run();
    }
}
