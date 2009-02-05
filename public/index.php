<?php

echo '<pre>';
print_r($_SERVER);
echo '</pre>';
exit(0);

try {

	require_once '../app/bootstrap.php';

} catch (Exception $e) {

	echo '<pre>';
	echo $e;
	echo '</pre>';

}
