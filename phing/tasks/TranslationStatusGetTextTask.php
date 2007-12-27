<?php
/**
* @package phing
*/

/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the LGPL. For more information please see
 * <http://phing.info>.
 */

require_once "phing/Task.php";

class TranslationStatusGetTextTask extends Task {

    protected $localedirectory = "locale"; // directory where translation are save
    protected $file = "messages"; //  translation file
    protected $xmloutput = "translation-status.xml"; // xml status file

    /**
    *
    * @param string root of local directory. By default it's locale
    */
	function setLocaledirectory($value)
	{
		$this->localedirectory = $value;
	}

    /**
    *
    * @param string file name output xml file of translation status
    */
	function setXmloutput($value)
	{
		$this->xmloutput = $value;
	}

	/**
    *
    * @param string file name for translations. By default it's messages
    */
	function setFile($value)
	{
		$this->file = $value;
	}

    /**
     * The main entry point method.
     */
    public function main() {
		$xml = new SimpleXMLElement("<?xml version='1.0'?><translations></translations>");
       if (!$dh = opendir($this->localedirectory)) {
        	throw new BuildException('Locale directory is not valid');
       }
        while (($lang = readdir($dh)) !== false) {
            if ($lang[0] != '.') {
                $src = $this->localedirectory.'/'.$lang.'/'.$this->file.'.po';
                if (!is_file($src)) {
                    throw new BuildException("File $src doesn't exist.");
                }
                $status = $this->getStatus($src);
				$status['lang'] = $lang;
				$elem = $xml->addChild('translation');
				$elem->fuzzy = $status['fuzzy'];
				$elem->strings = $status['strings'];
				$elem->lang = $status['lang'];
                $this->log("Translation status of $lang Strings: " . $status['strings'] . " Fuzzy: " . $status['fuzzy'], PROJECT_MSG_INFO);
            }
        }
        closedir($dh);
		file_put_contents($this->xmloutput, $xml->asXML());
    }

	/**
	* @return array Associative array with translation status key are:
	* fuzzy: Number of non translated string
	* strings: Number of strings
	*/
    private function getStatus($src)
    {
		$f = @fopen($src, 'r');
		if ($f === false) {
			throw new BuildException("Can't open $src.");
		}
		$res = array("fuzzy" => 0, "strings" => 0);
		while (!feof($f)) {
			$line = fgets($f);
			if (preg_match('/^msgid/', $line)) {
				$res['strings']++;
			}
			if (preg_match('/fuzzy/', $line)) {
				$res['fuzzy']++;
			}
		}
		fclose($f);
		return ($res);
	}
}

?>
