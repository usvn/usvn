<?php
require_once 'Zend/XmlRpc/Client.php';

$server = new Zend_XmlRpc_Client('http://www.upcdatabase.com/rpc');

$client = $server->getProxy();

print_r( $client->lookupUPC('071641301627') );
