<?php
try
{
  // Define path to application directory
  defined('APPLICATION_PATH')
      || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../app'));
  if (!defined('USVN_BASE_DIR'))
    define('USVN_BASE_DIR', realpath(dirname(__FILE__) . '/..'));

  // Define application environment
  defined('APPLICATION_ENV')
      || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

  // Ensure library/ is on include_path
  set_include_path(implode(PATH_SEPARATOR, array(
      realpath(APPLICATION_PATH . '/../library'),
      get_include_path(),
  )));

  /** Zend_Application */
  require_once 'Zend/Application.php';

  // Create application, bootstrap, and run
  $application = new Zend_Application(
      APPLICATION_ENV, 
      APPLICATION_PATH . '/configs/application.ini'
  );
  $toto = $application->bootstrap()
                      ->run();
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
	<h1>Error: <?php echo $e->getMessage() ?></h1>
	<?php if (defined('APPLICATION_ENV') && APPLICATION_ENV == 'development' ): ?>
	<h2>Trace:</h2>
	<table>
		<?php foreach ($e->getTrace() as $frame): ?>
		<tr>
			<td><?= (isset($frame['file']) && isset($frame['file']) ? $frame['file'] . ':' . $frame['line'] : '?') ?></td>
			<td><?= (isset($frame['class']) ? $frame['class'] . '::' : '') ?><?= $frame['function'] ?></td>
			<td>
				<? join(array_keys($frame), ', ') ?>
			</td>
		</tr>
		<?php endforeach ?>
	</table>
  <?php else: ?>
    <?php // message pour la prod ?>
	<?php endif ?>
</body>
</html>
<?php
}
