<?php
/**
 * Methods to turn request parameters into post-commit script files and vice
 * versa
 *
 * @author Klemen Vodopivec <klemen@vodopivec.org>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2008, Klemen Vodopivec
 * @since 0.6
 * @package usvn
 *
 * The development of this file and entire USVN mail-notification support was
 * supported through industry projects of Faculty of natural sciences and
 * mathematics, University of Maribor, Slovenia <http://www.fnm.uni-mb.si>
 *
 * $Id$
 */

require_once 'SVNUtils.php';

define("POST_COMMIT_NOTIFICATION_TMPL", ".notify_message");
define("POST_COMMIT_NOTIFICATION_RCPTS", ".notify_recipients");

class USVN_Notification
{
	private $_template_path;
	private $_recipients_path;
	private $_post_commit_path;
	private $_subject = "";
	private $_text = "";
	private $_recipients = array();
	public $enabled = false;

	/** 
	* @param string Project name
	* @throw USVN_Exception if project is invalid
	*/
	public function __construct($project = "")
	{
		if ($project != "") {
			$config = Zend_Registry::get('config');
			$project_path = $config->subversion->path
			. DIRECTORY_SEPARATOR
			. 'svn'
			. DIRECTORY_SEPARATOR
			. $project;
			if (!USVN_SVNUtils::isSVNRepository($project_path)) {
				throw new USVN_Exception(T_("No such project: %s"), $project_name);
			}
			$this->_template_path = $project_path . DIRECTORY_SEPARATOR . POST_COMMIT_NOTIFICATION_TMPL;
			$this->_recipients_path = $project_path . DIRECTORY_SEPARATOR . POST_COMMIT_NOTIFICATION_RCPTS;
			$this->_post_commit_path = $project_path . DIRECTORY_SEPARATOR . "hooks/post-commit";
			if (file_exists($this->_post_commit_path)) {
				$this->enabled = true;
			}
		}
	}

	/**
	 * Read template from mail file and parse subject and message
	 */
	public function readTemplate()
	{
		$lines = @file($this->_template_path);
		if ($lines !== FALSE) {
			$headers = true;
			foreach ($lines as $line) {
				if (!$headers) {
					$this->_text .= $line;
				} else if (strncasecmp($line, "subject:", 8) == 0) {
					$this->_subject = trim(substr($line, 9));
				} else if (trim($line) == "") {
					$headers = false;
				}
			}
		}
	}

	public function saveTemplate()
	{
		if ($this->_subject == "" || $this->_text == "") {
			throw new USVN_Exception(T_("No subject or text specified"));
		}
		$message = @file_get_contents($this->_template_path);
		// Try to update previous message, maybe some other headers are there
		if ($message !== FALSE) {
			$start = stripos($message, "subject:");
			$end = strpos($message, "\n", $start);
			if ($start !== FALSE && $end !== FALSE) {
				$message = substr_replace($message, "Subject: " . $this->_subject, $start, $end - $start);
			} else {
				$message = "Subject: " . $this->_subject . "\n" . $message;
			}

			$start = stripos($message, "from:");
			if ($start === FALSE) {
				$message .= "From: SVN autoreporter - do not reply <do.not.reply@example.com>";
			}

			$start = strpos($message, "\n\n");
			if ($start !== FALSE) {
				$message = substr_replace($message, "\n\n" . $this->_text, $start);
			} else {
				$message .= "\n\n" . $this->_text;
			}
		} else {
			$message = "Subject: " . $this->_subject . "\n";
			$message .= "\n";
			$message .= $this->_text;
		}
		if (!@file_put_contents($this->_template_path, $message)) {
			throw new USVN_Exception(T_("Can not save notification template file: %s"), $this->_template_path);
		}
	}

	public function deleteTemplate() {
		if (!@unlink($this->_template_path)) {
			throw new USVN_Exception(T_("Can not delete notification template file"));
		}
	}

	public function setSubject($subject)
	{
		if ($subject != "") {
			$this->_subject = $subject;
		}
	}

	public function getSubject()
	{
		return $this->_subject;
	}

	public function setText($text)
	{
		if ($text != "") {
			$this->_text = $text;
		}
	}

	public function getText()
	{
		return $this->_text;
	}

	private static function verifyEmail($var)
	{
		$email_regex = "^[-_a-z0-9]+(\.[-_a-z0-9]+)*@[-a-z0-9]+(\.[-a-z0-9]+)*\.[a-z]{2,6}$";
		return preg_match("/$email_regex/", $var);
	}

	public function readRecipients()
	{
		$recipients = @file($this->_recipients_path);
		if ($recipients !== FALSE)
		{
			foreach ($recipients as &$value) {
				$value = trim($value);
			}
			$this->_recipients = array_filter($recipients, array($this, "verifyEmail"));
		}
	}

	public function saveRecipients() {
		if (count($this->_recipients) == 0) {
			throw new USVN_Exception(T_("No recipients specified"));
		}
		// Simply overwrite all recipients
		$recipients = @implode("\n", $this->_recipients) . "\n";
		if (!@file_put_contents($this->_recipients_path, $recipients)) {
			throw new USVN_Exception(T_("Can not save notification recipients"));
		}
	}

	public function deleteRecipients() {
		if (!@unlink($this->_recipients_path)) {
			throw new USVN_Exception(T_("Can not delete notification recipients file"));
		}
	}

	public function addRecipient($recipient)
	{
		if ($this->verifyEmail($recipient)) {
			$this->_recipients[] = $recipient;
		} else {
			throw new USVN_Exception(T_("Invalid recipient email"));
		}
	}

	public function getRecipients()
	{
		return $this->_recipients;
	}

	public function enable()
	{
		$script_path=realpath(dirname(__FILE__) . "/../tools/svn-notify.sh");
		$str = "#!/bin/sh\n";
		$str .= $script_path . " \$1 \$2 " . $this->_recipients_path . " " . $this->_template_path . "\n";
		@file_put_contents($this->_post_commit_path, $str);
		@chmod($this->_post_commit_path, 0500);
		$this->enabled = @file_exists($this->_post_commit_path);
		return $this->enabled;
	}

	public function disable()
	{
		@unlink($this->_post_commit_path);
		$this->enabled = @file_exists($this->_post_commit_path);
		return !$this->enabled;
	}
}
?>
