<?php
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
//    protected $filesets = array(); // all fileset objects assigned to this task

    /**
    *
    * @param string root of local directory. By default it's locale
    */
	function setLocaledirectory($value)
	{
		$this->localedirectory = $value;
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
        if ($dir = opendir($name)) {
            while($file = readdir($dir)) {
				if (preg_match('/\.php$/', $file)) {
					$res[] = "$name/$file";
				}
				if(is_dir($file) && !in_array($file, array(".",".."))) {
					$res = array_merge ($res, $this->list_dir($file));
				}
			}
			closedir($dir);
		}
		return $res;
    }

    private function findStr($lang)
    {
		$savepath = getcwd();
        $files = $this->list_dir(".");
		chdir($this->localedirectory.'/'.$lang.'/LC_MESSAGES');
		rename("messages.po", "old.po");
		$command = "xgettext --keyword=T_ --keyword=T_ngettext";
		foreach ($files as $file) {
			$command .= " ../../../$file";
		}
		exec($command);
		$messagepo = file_get_contents("messages.po");
		$messagepo = str_replace('charset=CHARSET', 'charset=UTF-8', $messagepo);
		file_put_contents ("messages.po", $messagepo);
		exec("msgmerge old.po messages.po --output-file=messages.po");
		unlink("old.po");
 /*
        $project = $this->getProject();
        foreach($this->filesets as $fs) {
            $ds = $fs->getDirectoryScanner($project);
            $files = $ds->getIncludedFiles();
            $dir = $fs->getDir($this->project)->getPath();
            foreach($files as $file) {
                echo $dir.DIRECTORY_SEPARATOR.$file;
            }
        }
*/
		chdir($savepath);
    }
}

?>