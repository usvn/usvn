<?php
try
{
	require_once '../app/newBootstrap.php';
	Zend_Controller_Front::getInstance()->dispatch();
}
catch (Exception $e)
{
	@header("HTTP/1.0 500 Internal Error");
	@header('Content-type: text/html');
?>

<html>
<head>
	<meta http-equiv="Content-type" content="text/html; charset=utf-8">
	<title>500 Internal Error</title>
</head>
<body>
	<h1><?= $e->getMessage() ?></h1>
	<h2>Trace:</h2>
	<table>
		<? foreach ($e->getTrace() as $frame): ?>
		<tr>
			<td><?= (isset($frame['file']) && isset($frame['file']) ? $frame['file'] . ':' . $frame['line'] : '?') ?></td>
			<td><?= (isset($frame['class']) ? $frame['class'] . '::' : '') ?><?= $frame['function'] ?></td>
			<td>
				<? join(array_keys($frame), ', ') ?>
			</td>
		</tr>
		<? endforeach ?>
	</table>
</body>
</html>
<?php
}
