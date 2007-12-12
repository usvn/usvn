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

class FindStrGetTextTask extends Task {

    protected $localedirectory = "locale"; // directory where translation are save
	protected $scandirectory = ".";

    /**
    *
    * @param string root of locale directory. By default it's locale
    */
	function setLocaledirectory($value)
	{
		$this->localedirectory = $value;
	}

    /**
    * @param string root of directory to scan. By default it's .
    */
	function setScandirectory($value)
	{
		$this->scandirectory = $value;
	}
    /**
     * The main entry point method.
     */
    public function main() {
        /*if(count($this->filesets) == 0) {
            throw new BuildException("Missing either a nested fileset");
        }*/
        if (!$dh = opendir($this->localedirectory)) {
        	throw new BuildException('Locale directory is not valid');
        }
        while (($lang = readdir($dh)) !== false) {
            if ($lang[0] != '.') {
                $this->log("Find str for $lang", PROJECT_MSG_INFO);
                $this->findStr($lang);
            }
        }
        closedir($dh);
    }

    private function list_dir($name) {
		$res = array();
		echo $name."\n";
        if ($dir = opendir($name)) {
            while($file = readdir($dir)) {
				if (preg_match('/\.php$/', $file) || preg_match('/\.html$/', $file)  || preg_match('/\.phtml$/', $file)) {
					$res[] = "$name/$file";
				}
				if(is_dir($name."/".$file) && !in_array($file, array(".","..", ".svn"))) {
					$res = array_merge ($res, $this->list_dir($name."/".$file));
				}
			}
			closedir($dir);
		}
		return $res;
    }

    private function findStr($lang)
    {
		$savepath = getcwd();
        $files = $this->list_dir($this->scandirectory);
		chdir($this->localedirectory.'/'.$lang);
		rename("messages.po", "old.po");
		$command = "xgettext  --language=PHP --keyword=T_ --keyword=T_ngettext";
		foreach ($files as $file) {
			$command .= " " . escapeshellarg("../../../$file");
		}
		exec($command);
		$messagepo = file_get_contents("messages.po");
		$messagepo = str_replace('charset=CHARSET', 'charset=UTF-8', $messagepo);
		file_put_contents ("messages.po", $messagepo);
		exec("msgmerge old.po messages.po --output-file=messages.po");
		unlink("old.po");
		chdir($savepath);
    }
}

?>
