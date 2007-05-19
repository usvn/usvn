<?php
require_once('www/USVN/ConsoleUtils.php');

testFile('www/');

function testFile($path)
{
    if (file_exists($path)) {
        try {
            @$dir = new RecursiveDirectoryIterator($path);
        }
        catch(Exception $e) {
            return;
        }
        foreach(@new RecursiveIteratorIterator($dir) as $file) {
            if (!preg_match("/.*Zend.*/", $file)) {
                if (preg_match("/.*Test.php\$/", $file)) {
                    echo "Test $file : ";
                    $message = USVN_ConsoleUtils::runCmdCaptureMessage("php $file", $return);
                    if (strlen($message) && !preg_match('/OK \([\d]+ test[s]?\)/', $message)) {
                        echo "BAD\n";
                        echo $message;
                    }
                    else {
                        echo "OK\n";
                    }
                }
            }
        }
        $dir = NULL; // Else on windows that doesn't work....
    }
}
