<?php
/**
 * Main class of command line client
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.5
 * @package client
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This project has been realised as part of
 * end of studies project.
 *
 * $Id$
 */

class USVN_Client_Client
{
	/**
	 * Array fields:
	 * min: minimum number of args
	 * max: maximum number of args
	 * help: synopis
	 * description: What the command do
	 */
    private $commands = array(
        "install" => array('min' => 4, 'max' => 4, 'help' => '/svn/path http://usvn/url project auth', "description" => "Install hooks into svn repository"),
        "create" => array('min' => 4, 'max' => 4, 'help' => '/svn/path http://usvn/url project auth', "description" => "Create an svn repository"),
        "uninstall" => array('min' => 1, 'max' => 1, 'help' => '/svn/path', "description" => "Remove hooks from svn repository"),
        //"update" => array('min' => 1, 'max' => 1, 'help' => '/svn/path', "description" => "Update hooks from an svn repository"),
        "help" => array('min' => 0, 'max' => 1, 'help' => '[command]', "description" => "Display general help or specific help for a command"),
        "version" => array('min' => 0, 'max' => 0, 'help' => '', "description" => "Display version"),
    );

    public function __construct($args)
    {
        if (count($args) == 0)
        {
            throw new USVN_Exception($this->getHelp());
        }
        if (!array_key_exists($args[0], $this->commands))
        {
            throw new USVN_Exception($args[0].": unknow command\n".$this->getHelp());
        }
        if ((count($args) - 1) < $this->commands[$args[0]]['min'] || (count($args) - 1) > $this->commands[$args[0]]['max'])
        {
            throw new USVN_Exception($this->getCommandHelp($args[0]));
        }
        $cmd = array_shift($args);
        switch ($cmd)
        {
            case 'install':
                $this->cmdInstall($args);
            break;

            case 'create':
                $this->cmdCreate($args);
            break;

            case 'uninstall':
                $this->cmdUninstall($args);
            break;

            case 'help':
                $this->cmdDisplayHelp($args);
            break;

            case 'version':
                $this->display("USVN client version 0.5\n");
            break;
        }
    }

    private function cmdDisplayHelp($args)
    {
        if (count($args) == 1)
        {
            $this->display($this->getCommandHelp($args[0]));
        }
        else
        {
            $this->display($this->getHelp());
        }
    }

    private function getHelp()
    {
        $msg = "USVN commands:\n";
        foreach ($this->commands as $command => $tab)
        {
            $msg .= $command."\n";
        }
        $msg .= "\nType usvn help command_name for more informations.\n";
        return $msg;
    }

    private function getCommandHelp($command)
    {
        if (!array_key_exists($command, $this->commands))
        {
            return "Unknow command $command\n";
        }
        return "Usage: usvn ".$command." ".$this->commands[$command]["help"]."\n".
                    $this->commands[$command]["description"]."\n";
    }

    private function cmdInstall($args)
    {
        $path = $args[0];
        $url = $args[1];
        $project = $args[2];
        $auth = $args[3];
        new USVN_Client_Install($path, $url, $project, $auth);
    }

    private function cmdCreate($args)
    {
        $path = $args[0];
        $url = $args[1];
        $project = $args[2];
        $auth = $args[3];
        new USVN_Client_Create($path, $url, $project, $auth);
    }

    private function cmdUninstall($args)
    {
        $path = $args[0];
        new USVN_Client_Uninstall($path);
    }

    /**
	 * How the client display result.
	 *
	 * @var string
	 */
    protected function display($str)
    {
        echo $str;
    }
}
