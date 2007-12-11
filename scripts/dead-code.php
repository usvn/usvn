<?php
$used_function = array();
$declare_function = array();

function get_functions($file)
{
	global $used_function;
	global $declare_function;

	$source = file_get_contents($file);
	$tokens = token_get_all($source);
	$next_is_function = false;
	$last_string = "";

	foreach ($tokens as $token) {
		if (is_string($token)) {
			if ($token == '(') {
				if ($next_is_function) {
					$next_is_function = false;
				}
				else {
					$used_function[] = $last_string;
				}
			}
		}
		else {
			list($id, $text) = $token;
			switch ($id) {
				case T_FUNCTION:
					$next_is_function = true;
					break;
				case T_STRING:
					$last_string = $text;
					if ($next_is_function) {
						if ($text != '__get' && $text != '__set' && $text != '_insert' && !preg_match("#.*Action#", $text)) {
							$declare_function[$file][] =$text;
						}
					}
					break;
			}
		}
	}
}

$path = "www";
do
{
	$files = glob($path . "/*.ph*");
	foreach ($files as $file) {
		if (!preg_match("#.*Zend.*#", $file) && !preg_match("#.*Test.php#", $file)) {
			//echo "Parse: " . $file . "\n";
			get_functions($file);
		}
	}
	$path .= "/*";
} while (count($files));

foreach ($declare_function as $file => $list_f) {
	foreach ($list_f as $f) {
		if (array_search($f, $used_function) === false) {
			echo "Unused $f in $file\n";
		}
	}
}
