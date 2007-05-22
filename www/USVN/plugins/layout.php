<?php
/**
 * Add header and footer to USVN pages
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.5
 * @package usvn
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This project has been realised as part of
 * end of studies project.
 *
 * $Id$
 */
class layout extends Zend_Controller_Plugin_Abstract
{
	/**
	 * Doit gerer le layout de la page HTML
	 *
	 */
	public function dispatchLoopShutdown()
	{
		$response = $this->getResponse();
		$headers = $response->getHeaders();
		foreach ($headers as $header) {
			if (strcasecmp($header['name'], "Content-Type") == 0) {
				if (strcasecmp($header['value'], "text/html") == 0) {
					$this->addHeader($response);
					$this->addFooter($response);
				}
				break;
			}
		}
	}

	/**
	* @param array
	* @return string
	*/
	private function buildMenu($menu_items, $css_id)
	{
		if (count($menu_items) == 0) {
			return "";
		}
		$str = "<ul id=\"$css_id\">\n";
		$base_url = $this->getRequest()->getBaseUrl();
		$path = $this->getRequest()->getPathInfo();
		foreach ($menu_items as $elem) {
			$img = '';
			$selected = '';
			if (strncmp("/" . $elem['link'], $path, strlen($elem['link']) + 1) === 0) {
				if (strlen($elem['link']) != 0 || $path == '/' || strlen($path) == 0) {
					$selected = " id=\"{$css_id}_selected\"";
				}
			}
			if (isset($elem["image"]) && !empty($elem["image"])) {
				$img = "<img src=\"{$elem["image"]}\">";
			}
			$str .= "<li$selected><a href=\"$base_url/{$elem["link"]}\">$img{$elem["title"]}</a></li>\n";
		}
		return $str . "</ul>\n";
	}

	/**
	 * Add header text
	 *
	 * @param Zend_Controller_Response_Abstract $response
	 */
	protected function addHeader($response)
	{
		$config = Zend_Registry::get('config');
		$homepage = T_("Homepage");
		$identity = Zend_Auth::getInstance()->getIdentity();
		$loggedAs = !($identity === null) ? T_("Logged as") . " " . $identity['username'] : "";
		$menu = new USVN_Menu(USVN_MENUS_DIR, $this->getRequest(), $identity);
		$header = <<<EOF
<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	    <title>{$config->site->title}</title>
		<meta http-equiv="Content-Type"	content="text/html; charset=utf-8" />
		<link rel="icon" href="{$base_url}/{$config->site->ico}" type="image/x-icon" />
		<link type="text/css" rel="stylesheet" media="screen" href="{$config->url->base}/css/screen" />
		<link type="text/css" rel="stylesheet" media="print" href="{$config->url->base}/css/print" />
		<script type="text/javascript" src="{$config->url->base}/js/"></script>
	</head>
	<body>
		<div id="usvn_page">
			<div id="usvn_header">
				<a id="usvn_logo" href="{$config->url->base}/">
					<img src="{$config->url->base}/{$config->site->logo}" alt="{$homepage}" />
				</a>
			</div>
EOF;
		$header .= $this->buildMenu($menu->getTopMenu(), 'usvn_menu');
		$header .= $this->buildMenu($menu->getSubMenu(), 'usvn_submenu');
		$header .= $this->buildMenu($menu->getSubSubMenu(), 'usvn_subsubmenu');
		$header .= "<div id=\"usvn_content\">";
		$body = $response->getBody(true);
		$response->setBody($header);
		foreach ($body as $text) {
			$response->appendBody($text);
		}
	}

	/**
	 * Add footer text
	 *
	 * @param Zend_Controller_Response_Abstract $response
	 */
	protected function addFooter($response)
	{
		$base_url = $this->getRequest()->getBaseUrl();
		$footer = <<<EOF
			<script type='text/javascript'>
				lookForHTMLTableTools(HTMLTableToolsOptions);
			</script>
			</div><br />
			<div id="usvn_footer"><h5>Powered by USVN</h5></div>
		</div>
	</body>
</html>
EOF;
		$response->appendBody($footer);
	}
}
