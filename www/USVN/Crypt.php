<?php
/**
 * Crypt password
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.6
 * @package Un_package_par_exemple_client
 * @subpackage Le_sous_package_par_exemple_hooks
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This project has been realised as part of
 * end of studies project.
 *
 * $Id$
 */
class USVN_Crypt
{
	/**
	 * @param string Password
	 * @return Encrypt password
	 */
	static public function crypt($password)
	{
		return crypt($password, $password);
	}

	/**
	 * Check if a clear password match encrypt password
	 *
	 * @param string
	 * @param string
	 * @return bool
	 */
	static public function checkPassword($clear, $encrypt)
	{
		if (crypt($clear, $encrypt) == $encrypt) {
			return true;
		}
		return false;
	}

	/**
	 * Apache's modified MD5 algorithm
	 *
	 * @param string $plainpasswd
	 * @param string|null $salt
	 * @return string
	 */
	static public function _cryptApr1MD5($plainpasswd, $salt = null) {
		if ($salt === null) {
			$salt = substr(str_shuffle("abcdefghijklmnopqrstuvwxyz0123456789"), 0, 8);
		} else {
			if (substr($salt, 0, 6) == '$apr1$') {
				$salt = substr($salt, 6, 8);
			} else {
				$salt = substr($salt, 0, 8);
			}
		}
		$len = strlen($plainpasswd);
		$text = $plainpasswd.'$apr1$'.$salt;
		$bin = pack("H32", md5($plainpasswd.$salt.$plainpasswd));
		for($i = $len; $i > 0; $i -= 16) { $text .= substr($bin, 0, min(16, $i)); }
		for($i = $len; $i > 0; $i >>= 1) { $text .= ($i & 1) ? chr(0) : $plainpasswd{0}; }
		$bin = pack("H32", md5($text));
		for($i = 0; $i < 1000; $i++) {
			$new = ($i & 1) ? $plainpasswd : $bin;
			if ($i % 3) $new .= $salt;
			if ($i % 7) $new .= $plainpasswd;
			$new .= ($i & 1) ? $bin : $plainpasswd;
			$bin = pack("H32", md5($new));
		}
		$tmp = "";
		for ($i = 0; $i < 5; $i++) {
			$k = $i + 6;
			$j = $i + 12;
			if ($j == 16) $j = 5;
			$tmp = $bin[$i].$bin[$k].$bin[$j].$tmp;
		}
		$tmp = chr(0).chr(0).$bin[11].$tmp;
		$tmp = strtr(strrev(substr(base64_encode($tmp), 2)),
		"ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/",
		"./0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz");
		return "$"."apr1"."$".$salt."$".$tmp;
	}

}
?>
