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
 * @copyright  Copyright (c) 2006-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

require_once 'Zend/Loader.php';
Zend_Loader::loadClass('Zend_Gdata');
Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
Zend_Loader::loadClass('Zend_Gdata_Spreadsheets');
Zend_Loader::loadClass('Zend_Http_Client');

class SimpleCRUD
{
    
    public function __construct($email, $password)
    {
        $client = Zend_Gdata_ClientLogin::getHttpClient($email, $password, 
                Zend_Gdata_Spreadsheets::AUTH_SERVICE_NAME);
        $this->gdClient = new Zend_Gdata_Spreadsheets($client);
        $this->currKey = '';
        $this->currWkshtId = '';
        $this->listFeed = '';
    }
    
    public function promptForSpreadsheet()
    {
        $feed = $this->gdClient->getSpreadsheetFeed();
        $this->printFeed($feed);
        $input = getInput("\nSelection");
        $currKey = split('/', $feed->entries[$input]->id->text);
        $this->currKey = $currKey[5];
    }
    
    public function promptForWorksheet()
    {
        $query = new Zend_Gdata_Spreadsheets_DocumentQuery();
        $query->setSpreadsheetKey($this->currKey);
        $feed = $this->gdClient->getWorksheetFeed($query);
        $this->printFeed($feed);
        $input = getInput("\nSelection");
        $currWkshtId = split('/', $feed->entries[$input]->id->text);
        $this->currWkshtId = $currWkshtId[8];
    }
    
    public function promptForCellsAction()
    {
        echo "\ndump\nupdate {row} {col} {input_value}\n";
        $input = getInput('Command: ');
        $command = split(' ', $input);
        if ($command[0] == 'dump') {
            $this->cellsGetAction();
        } else if ($command[0] == 'update') {
            if (count($command) == 4) {
                $this->cellsUpdateAction($command[1], $command[2], $command[3]);
            } else {
                $this->cellsUpdateAction($command[1], $command[2], '');
            }
        } else {
            $this->invalidCommandError($input);
        }
    }
    
    public function promptForListAction()
    {
        echo "dump\ninsert {row_data} (example: insert label=content)\n".
                "update {row_index} {row_data}\ndelete {row_index}\n\n";
        $input = getInput('Command');
        $command = split(' ', $input);
        if ($command[0] == 'dump') {
            $this->listGetAction();
        } else if ($command[0] == 'insert') {
            $this->listInsertAction(array_slice($command, 1));
        } else if ($command[0] == 'update') {
            $this->listUpdateAction($command[1], array_slice($command, 2));
        } else if ($command[0] == 'delete') {
            $this->listDeleteAction($command[1]);
        } else {
            $this->invalidCommandError($input);
        }
    }
    
    public function cellsGetAction()
    {
        $query = new Zend_Gdata_Spreadsheets_CellQuery();
        $query->setSpreadsheetKey($this->currKey);
        $query->setWorksheetId($this->currWkshtId);
        $feed = $this->gdClient->getCellFeed($query);
        $this->printFeed($feed);
    }
    
    public function cellsUpdateAction($row, $col, $inputValue)
    {
        $entry = $this->gdClient->updateCell($row, $col, $inputValue, 
                $this->currKey, $this->currWkshtId);
        if ($entry instanceof Zend_Gdata_Spreadsheets_CellEntry) {
            echo "Success!\n";
        }
    }
    
    public function listGetAction()
    {
        $query = new Zend_Gdata_Spreadsheets_ListQuery();
        $query->setSpreadsheetKey($this->currKey);
        $query->setWorksheetId($this->currWkshtId);
        $this->listFeed = $this->gdClient->getListFeed($query);
        $this->printFeed($this->listFeed);
    }
    
    public function listInsertAction($rowData)
    {
        $rowArray = $this->stringToArray($rowData);
        $entry = $this->gdClient->insertRow($rowArray, $this->currKey, $this->currWkshtId);
        if ($entry instanceof Zend_Gdata_Spreadsheets_ListEntry) {
            echo "Success!\n";
        }
    }
    
    public function listUpdateAction($index, $rowData)
    {
        $query = new Zend_Gdata_Spreadsheets_ListQuery();
        $query->setSpreadsheetKey($this->currKey);
        $query->setWorksheetId($this->currWkshtId);
        $this->listFeed = $this->gdClient->getListFeed($query);
        $rowArray = $this->stringToArray($rowData);
        $entry = $this->gdClient->updateRow($this->listFeed->entries[$index], $rowArray);
        if ($entry instanceof Zend_Gdata_Spreadsheets_ListEntry) {
            echo "Success!\n";
        }
    }
    
    public function listDeleteAction($index)
    {
        $query = new Zend_Gdata_Spreadsheets_ListQuery();
        $query->setSpreadsheetKey($this->currKey);
        $query->setWorksheetId($this->currWkshtId);
        $this->listFeed = $this->gdClient->getListFeed($query);
        $this->gdClient->deleteRow($this->listFeed->entries[$index]);
    }
    
    public function stringToArray($rowData)
    {
        $arr = array();
        foreach ($rowData as $row) {
            $temp = split('=', $row);
            $arr[$temp[0]] = $temp[1];
        }
        return $arr;
    }
    
    public function printFeed($feed)
    {
        $i = 0;
        foreach($feed->entries as $entry) {
            if ($entry instanceof Zend_Gdata_Spreadsheets_CellEntry) {
                print $entry->title->text .' '. $entry->content->text . "\n";
            } else if ($entry instanceof Zend_Gdata_Spreadsheets_ListEntry) {
                print $i .' '. $entry->title->text .' '. $entry->content->text . "\n";
            } else {
                print $i .' '. $entry->title->text . "\n";
            }
            $i++;
        }
    }
    
    public function invalidCommandError($input)
    {
        echo 'Invalid input: '.$input."\n";
    }
    
    public function run()
    {
        $this->promptForSpreadsheet();
        $this->promptForWorksheet();
        $input = getInput('cells or list');
        if ($input == 'cells') {
            while(1) {
                $this->promptForCellsAction();
            }
        } else if ($input == 'list') {
            while(1) {
                $this->promptForListAction();
            }
        }
    }
    
}


function getInput($text)
{
    echo $text.': ';
    return trim(fgets(STDIN));
}

$user = null;
$pass = null;

// process command line options
foreach ($argv as $argument) {
    $argParts = split('=', $argument);
    if ($argParts[0] == '--user') {
        $user = $argParts[1];
    } else if ($argParts[0] == '--pass') {
        $pass = $argParts[1];
    }
}

if (($user == null) || ($pass == null)) {
    exit('php spreadsheetExample.php -- --user=[username] --pass=[password]');
}

$sample = new SimpleCRUD($user, $pass); 
$sample->run();


?>
