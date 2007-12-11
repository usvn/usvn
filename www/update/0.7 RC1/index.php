<?php
/**
 * Root for upgrade
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.7 RC2
 * @package update
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This project has been realised as part of
 * end of studies project.
 *
 * $Id$
 */


define('USVN_CONFIG_FILE', "../../config.ini");
define('USVN_HTACCESS_FILE', "../../.htaccess");

header("Content-encoding: UTF-8");

set_include_path(get_include_path() .PATH_SEPARATOR ."../../");
require_once 'USVN/autoload.php';
USVN_Translation::initTranslation('en_US', '../../locale');

if (isset($_POST['update'])) {
	try {
		$config = new USVN_Config_Ini(USVN_CONFIG_FILE, 'general');
		$config->version = "0.7 RC1";
		include dirname(__FILE__) . '/db.php';
		upgrade_sql($config);
		$config->update = array("checkforupdate" => $_POST['update'], "lastcheckforupdate" => 0);
		$config->save();
	}
	catch (Exception $e) {
		echo "<h1>Update error</h1>";
		echo $e->getMessage();
		exit(1);
	}

	header("Location: ../../");
}
else {
	echo <<<EOF
<h1>Update USVN</h1>
<p>During update checks, USVN will send information regarding of your installation.</p>
<p>
This information is only collected for statistical purposes and allow
us to better know the platforms on which USVN is installed.
</p>
<p>
Here is a list of what we want to collect:
<ul>
<li>IP (to get geographical position statistics of USVN in the world)</li>
<li>The PHP version</li>
<li>The SVN version</li>
<li>The USVN version</li>
<li>The contents of phpinfo()</li>
<li>The used database driver</li>
<li>The used translation</li>
</ul>
</p>
<p>
If you do not want this information to be collected, you juste have to
disable the update notification.
</p>
</div>
<hr />
<form method="post">
	<input type="hidden" name="update" value="1" />
	<input type="submit" value="Allow check update and send statistics informations" />
</form>
&nbsp;
<form name="createAdmin" action="index.php?step=8" method="post">
	<input type="hidden" name="update" value="0" />
	<input type="submit" value="Don't check update" />
</form>
EOF;
}
