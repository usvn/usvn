#!/usr/bin/env php
<?php
/**
 * Installation via CLI
 *
 * @author Thibault Martin-Lagardette <contact@usvn.info>
 * @version 1.0
 * @copyright USVN Team, 29 September, 2009
 * @package install
 **/

require_once realpath(dirname(__FILE__) . '/install.includes.php');

function display_help($options)
{
    $basename = basename(__FILE__);
    echo <<<EOF
Usage: ./{$basename} [OPTIONS]

{$basename} is the CLI version of the USVN installer.
It is not as complete as the web installer, since the goal of the CLI installer
is to create a configuration out of a "configuration template"

Mandatory arguments:
    -c, --config-file <FILE>        The path to the template configuration file
    -l, --admin-login <LOGIN>       USVN admin login
    -p, --admin-password <PASSWORD> USVN admin password

Optional arguments:
    -t, --server-useHTTPS           Does the server use HTTPS? (default: false)
    -s, --server-host <HOST>        The server host (default: '{$options['server-host']}')
    -u, --usvn-url-path <URL_PATH>  The path to USVN (default: '{$options['usvn-url-path']}')
    -a, --htaccess-file <FILE>      The path to the generated .htaccess file (default: '{$options['htaccess-file']}')
    -d, --database-path <FILE>      The path to the database install files (default: '{$options['database-path']}')
    -f, --admin-firstname <NAME>    USVN admin first name (default: '{$options['admin-firstname']}')
    -l, --admin-lastname <NAME>     USVN admin last name (default: '{$options['admin-lastname']}')
    -e, --admin-email <EMAIL>       USVN admin email (default: '{$options['admin-email']}')
    -o, --output-apache-config      Output the apache config when the installer is done (default: false)


EOF;
    exit(0);
}

$options = array(
    'server-useHTTPS' => false,
    'server-host' => 'localhost',
    'usvn-url-path' => '/usvn',
    'htaccess-file' => USVN_PUB_DIR,
    'database-path' => USVN_APP_DIR . '/install/sql/',
    'admin-firstname' => 'System',
    'admin-lastname' => 'Administrator',
    'admin-email' => '',
    'output-apache-config' => false
);

$mandatory_options = array(
    'config-file',
    'admin-login',
    'admin-password'
);

/*
 * Using for+switch because getopt() might not work properly with PHP < 5.3:
 * - Longopts might not work on some systems
 * - No support for optional arguments (...)
 */
for ($i = 0; $i < $argc; $i++)
{
    switch (strtolower($argv[$i]))
    {
        case '-h':
        case '--help':
            display_help($options);
            break;

        case '-o':
        case '--output-apache-config':
            $options['output-apache-config'] = true;
            break;

        case '-c':
        case '--config-file':
            $options['config-file'] = $argv[++$i];
            break;

        case '-a':
        case '--htaccess':
            $options['htaccess-file'] = $argv[++$i];
            break;

        case '-l':
        case '--admin-login':
            $options['admin-login'] = $argv[++$i];
            break;

        case '-p':
        case '--admin-password':
            $options['admin-password'] = $argv[++$i];
            break;

        case '-f':
        case '--admin-firstname':
            $options['admin-firstname'] = $argv[++$i];
            break;

        case '-n':
        case '--admin-lastname':
            $options['admin-lastname'] = $argv[++$i];
            break;

        case '-e':
        case '--admin-email':
            $options['admin-email'] = $argv[++$i];
            break;

        case '-t':
        case '--server-usehttps':
            $options['server-useHTTPS'] = true;
            break;
            
        case '-s':
        case '--server-host':
            $options['server-host'] = $argv[++$i];
            break;

        case '-u':
        case '--usvn-path':
            $options['usvn-url-path'] = $argv[++$i];
            break;

        case '-d':
        case '--database-path':
            $options['database-path'] = $argv[++$i];
            break;
    }
}

foreach ($mandatory_options as $mandatory_option)
{
    if (!array_key_exists($mandatory_option, $options) || empty($options[$mandatory_option]))
    {
        echo 'Error: "' . $mandatory_option . '" is a mandatory option, and it was not specified.' . "\n";
        echo 'Aborting.' . "\n";
        exit(1);
    }
}

try
{
    if (!Install::installPossible(USVN_CONFIG_FILE))
    {
        echo 'Error: USVN is already installed.' . "\n";
        echo 'Aborting.' . "\n";
        exit(1);
    }
    USVN_Translation::initTranslation($GLOBALS['language'], USVN_APP_DIR . '/locale');

    $config = new USVN_Config_Ini($options['config-file'], USVN_CONFIG_SECTION);
    Zend_Registry::set('config', $config);

    /*
     * Those "echo"s are ugly, but anonymous functions are only available in PHP since versions >= 5.3.0 :(
     * It would have been nice to be able to do:
     *
     * installation_configure('site settings', function()
     * {
     *     Install::installConfiguration(USVN_CONFIG_FILE, $config->site->title);
     * });
     * installation_configure('subversion', function()
     * {
     *     Install::installSubversion(USVN_CONFIG_FILE, $config->subversion->path, $config->subversion->passwd, $config->subversion->authz, $config->subversion->url);
     * });
     *
     * ... etc.
     */
    echo 'Configuring URL... ';
    Install::installUrl(USVN_CONFIG_FILE, $options['htaccess-file'], $options['usvn-url-path'], $options['server-host'], $options['server-useHTTPS']);
    echo 'Done!' . "\n";
    echo 'Configuring language... ';
    Install::installLanguage(USVN_CONFIG_FILE, $config->translation->locale);
	Install::installTimezone(USVN_CONFIG_FILE, $config->timezone);
	Install::installLocale(USVN_CONFIG_FILE);
    echo 'Done!' . "\n";
    echo 'Configuring site settings... ';
    Install::installConfiguration(USVN_CONFIG_FILE, $config->site->title);
    echo 'Done!' . "\n";
    echo 'Configuring subversion... ';
    Install::installSubversion(USVN_CONFIG_FILE, $config->subversion->path, $config->subversion->passwd, $config->subversion->authz, $config->subversion->url);
    echo 'Done!' . "\n";
    echo 'Configuring database... ';
    Install::installDb(USVN_CONFIG_FILE, $options['database-path'], $config->database->options->host, $config->database->options->username, $config->database->options->password, $config->database->options->dbname, $config->database->prefix, $config->database->adapterName, true);
    echo 'Done!' . "\n";
    echo 'Configuring admin account... ';
    Install::installAdmin(USVN_CONFIG_FILE, $options['admin-login'], $options['admin-password'], $options['admin-firstname'], $options['admin-lastname'], $options['admin-email']);
    echo 'Done!' . "\n";
    Install::installEnd(USVN_CONFIG_FILE);
    echo "\n" . 'Installation is finished!' . "\n";
    if ($options['output-apache-config'])
    {
        echo 'Here is the Apache configuration. (You might need to restart your server)' . "\n\n";
        echo Install::getApacheConfig(USVN_CONFIG_FILE);
    }
}
catch (USVN_Exception $e)
{
    echo $e->getMessage();
    exit(1);
}

?>