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

class CompilationGetTextTask extends Task {

    protected $localedirectory = "locale"; // directory where translation are save
    protected $file = "messages"; //  translation file

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
       if (!$dh = opendir($this->localedirectory)) {
        	throw new BuildException('Locale directory is not valid');
       }
        while (($lang = readdir($dh)) !== false) {
            if ($lang[0] != '.') {
                $this->log("Compilation of $lang", PROJECT_MSG_INFO);
                $src = $this->localedirectory.'/'.$lang.'/'.$this->file.'.po';
                $dst = $this->localedirectory.'/'.$lang.'/'.$this->file.'.mo';
                if (!is_file($src)) {
                    throw new BuildException("File $src doesn't exist.");
                }
                $this->compilFile($src, $dst);
            }
        }
        closedir($dh);
    }

    private function compilFile($src, $dst)
    {
        $command = "msgfmt $src --output=$dst";
        $message = array();
        $ret = 0;
        exec($command, $message, $ret);
        if ($ret) {
            throw new BuildException("File $src compilation error.");
        }
    }
}

?>
