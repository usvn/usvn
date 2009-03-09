<?php

$USVNLogArray = array();
function USVNLogObject($name, $value)
{
	global $USVNLogArray;

	$USVNLogArray[] = array('name' => $name, 'value' => $value);
}

function T_($str)
{
	return USVN_Translation::_($str);
}
