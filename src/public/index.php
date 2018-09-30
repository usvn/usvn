<?php
try
{
	require_once '../app/bootstrap.php';
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
	<h1><?php echo htmlentities($e->getMessage()); ?></h1>
	<h2>Trace:</h2>
	<table>
		<?php foreach ($e->getTrace() as $frame): ?>
		<tr>
			<td><?php echo (isset($frame['file']) && isset($frame['file']) ? $frame['file'] . ':' . $frame['line'] : '?'); ?></td>
			<td><?php echo (isset($frame['class']) ? $frame['class'] . '::' : ''); ?><?php echo $frame['function'] ?></td>
			<td>
				<?php join(array_keys($frame), ', '); ?>
			</td>
		</tr>
		<?php endforeach; ?>
	</table>
</body>
</html>
<?php
}
