<?php
/**
 * This source file is subject to the Magento Integration Platform License
 * that is bundled with this package in the file LICENSE_MIP.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.flagbit.de/license/mip
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to magento@flagbit.de so we can send you a copy immediately.
 *
 * The Magento Integration Platform is a property of Flagbit GmbH & Co. KG.
 * It is NO part or deravative version of Magento and as such NOT published
 * as Open Source. It is NOT allowed to copy, distribute or change the
 * Magento Integration Platform or any of its parts. If you wish to adapt
 * the software to your individual needs, feel free to contact us at
 * http://www.flagbit.de or via e-mail (magento@flagbit.de) or phone
 * (+49 (0)800 FLAGBIT (3524248)).
 *
 *
 *
 * Dieser Quelltext unterliegt der Magento Integration Platform License,
 * welche in der Datei LICENSE_MIP.txt innerhalb des MIP Paket hinterlegt ist.
 * Sie ist außerdem über das World Wide Web abrufbar unter der Adresse:
 * http://www.flagbit.de/license/mip
 * Falls Sie keine Kopie der Lizenz erhalten haben und diese auch nicht über
 * das World Wide Web erhalten können, senden Sie uns bitte eine E-Mail an
 * magento@flagbit.de, so dass wir Ihnen eine Kopie zustellen können.
 *
 * Die Magento Integration Platform ist Eigentum der Flagbit GmbH & Co. KG.
 * Sie ist WEDER Bestandteil NOCH eine derivate Version von Magento und als
 * solche nicht als Open Source Softeware veröffentlicht. Es ist NICHT
 * erlaubt, die Software als Ganze oder in Einzelteilen zu kopieren,
 * verbreiten oder ändern. Wenn Sie eine Anpassung der Software an Ihre
 * individuellen Anforderungen wünschen, kontaktieren Sie uns unter
 * http://www.flagbit.de oder via E-Mail (magento@flagbit.de) oder Telefon
 * (+49 (0)800 FLAGBIT (3524248)).
 *
 *
 *
 * @package     Flagbit
 * @subpackage  Flagbit_Mip
 * @copyright   2009 by Flagbit GmbH & Co. KG
 * @author      Flagbit Magento Team <magento@flagbit.de>
 */


/**
 * @package     Flagbit
 * @subpackage  Flagbit_Mip
 */
class Flagbit_Mip_Model_Server_Trigger_Ftp extends Flagbit_Mip_Model_Server_Trigger_Abstract {


    /**#@+ FTP constant alias */
    const ASCII = FTP_ASCII;
    const TEXT = FTP_TEXT;
    const BINARY = FTP_BINARY;
    const IMAGE = FTP_IMAGE;
    const TIMEOUT_SEC = FTP_TIMEOUT_SEC;
    const AUTOSEEK = FTP_AUTOSEEK;
    const AUTORESUME = FTP_AUTORESUME;
    const FAILED = FTP_FAILED;
    const FINISHED = FTP_FINISHED;
    const MOREDATA = FTP_MOREDATA;
    /**#@-*/

    private static $aliases = array(
        'sslconnect' => 'ssl_connect',
        'getoption' => 'get_option',
        'setoption' => 'set_option',
        'nbcontinue' => 'nb_continue',
        'nbfget' => 'nb_fget',
        'nbfput' => 'nb_fput',
        'nbget' => 'nb_get',
        'nbput' => 'nb_put',
    );

    /** @var resource */
    private $resource;

    /** @var array */
    private $state;

    /** @var string */
    private $errorMsg;

    /**
     * is the ftp extension loaded?
     */
    public function __construct()
    {
        if (!extension_loaded('ftp')) {
            throw new Exception("PHP extension FTP is not loaded.");
        }
    }


    /**
     * run Trigger
     * determines Request Type Input / Output
     */
    public function run()
    {
        // connect
        Mage::helper('mip/log')->getWriter($this)->trace('FTP: connect to '.$this->_getDefinition()->getSettings('ftp_host'));
        $this->connect($this->_getDefinition()->getSettings('ftp_host'), 21, 10);

        // login
        $this->login(
            $this->_getDefinition()->getSettings('ftp_user'),
            $this->_getDefinition()->getSettings('ftp_pass')
        );

        switch($this->getDirection()){

            case 'input':
                //get file content
                $filesArray = explode(';', $this->_getDefinition()->getSettings('file'));
                $filesArray = $this->handleParams($filesArray);

                foreach($filesArray as $file){
                    $fileName = basename($file);
                    $filePath = dirname($file);

                    if($filePath){
                        if(!$this->isDir($filePath)){
                            Mage::throwException('The remote dir "'.$filePath.'" do not exists');
                        }
                        $this->chdir($filePath);
                    }
                    $temp = tmpfile();
                    $this->fget($temp, $fileName, self::ASCII);
                    fseek($temp, 0);
                    $contents[] = stream_get_contents($temp);
                    fclose($temp);

                    if($this->_getDefinition()->getSettings('delete_file')){
                        Mage::helper('mip/log')->getWriter($this)->trace('delete File: '.$fileName);
                        $this->delete($fileName);
                    }
                }
                if(count($contents) <= 1){
                    $contents = isset($contents[0]) ? $contents[0] : '';
                }
                $this->input($contents);
                break;

            case 'output':
                $output = $this->output((array) $this->handleParams($this->_getDefinition()->getSettings('params')));
                $filesArray = explode(';', $this->_getDefinition()->getSettings('file'));
                $filesArray = $this->handleParams($filesArray);

                foreach($filesArray as $file){

                    $fileName = basename($file);
                    $filePath = dirname($file);

                    if($filePath){
                        if(!$this->isDir($filePath)){
                            Mage::throwException('The remote dir "'.$filePath.'" do not exists');
                        }
                        $this->chdir($filePath);
                    }

                    $temp = tmpfile();
                    $fp = fopen($temp, "w+");
                    fwrite($fp, $output);
                    $this->fput($fileName, $fp, self::ASCII);
                    fclose($fp);
                    fclose($temp);
                }
                break;
        }
    }


    /**
     * Magic method (do not call directly).
     * @param  string  method name
     * @param  array   arguments
     * @return mixed
     * @throws Exception
     */
    public function __call($name, $args)
    {
        $name = strtolower($name);
        $silent = strncmp($name, 'try', 3) === 0;
        $func = $silent ? substr($name, 3) : $name;
        $func = 'ftp_' . (isset(self::$aliases[$func]) ? self::$aliases[$func] : $func);

        if (!function_exists($func)) {
            throw new Exception("Call to undefined method Ftp::$name().");
        }

        $this->errorMsg = NULL;

        if ($func === 'ftp_connect' || $func === 'ftp_ssl_connect') {
            $this->state = array($name => $args);
            $this->resource = call_user_func_array($func, $args);
            $res = NULL;

        } elseif (!is_resource($this->resource)) {
            restore_error_handler();
            throw new Exception("Not connected to FTP server. Call connect() or ssl_connect() first. (".$name.")");

        } else {
            if ($func === 'ftp_login' || $func === 'ftp_pasv') {
                $this->state[$name] = $args;
            }

            array_unshift($args, $this->resource);
            $res = call_user_func_array($func, $args);

            if ($func === 'ftp_chdir' || $func === 'ftp_cdup') {
                $this->state['chdir'] = array(ftp_pwd($this->resource));
            }
        }

        if (!$silent && $this->errorMsg !== NULL) {
            if (ini_get('html_errors')) {
                $this->errorMsg = html_entity_decode(strip_tags($this->errorMsg));
            }

            if (($a = strpos($this->errorMsg, ': ')) !== FALSE) {
                $this->errorMsg = substr($this->errorMsg, $a + 2);
            }

            throw new Exception($this->errorMsg);
        }

        return $res;
    }


    /**
     * Reconnects to FTP server.
     * @return void
     */
    public function reconnect()
    {
        @ftp_close($this->resource); // intentionally @
        foreach ($this->state as $name => $args) {
            call_user_func_array(array($this, $name), $args);
        }
    }



    /**
     * Checks if file or directory exists.
     * @param  string
     * @return bool
     */
    public function fileExists($file)
    {
        return is_array($this->nlist($file));
    }



    /**
     * Checks if directory exists.
     * @param  string
     * @return bool
     */
    public function isDir($dir)
    {
        $current = $this->pwd();
        try {
            $this->chdir($dir);
        } catch (Exception $e) {
        }
        $this->chdir($current);
        return empty($e);
    }



    /**
     * Recursive creates directories.
     * @param  string
     * @return void
     */
    public function mkDirRecursive($dir)
    {
        $parts = explode('/', $dir);
        $path = '';
        while (!empty($parts)) {
            $path .= array_shift($parts);
            try {
                if ($path !== '') $this->mkdir($path);
            } catch (Exception $e) {
                if (!$this->isDir($path)) {
                    throw new Exception("Cannot create directory '$path'.");
                }
            }
            $path .= '/';
        }
    }



    /**
     * Recursive deletes path.
     * @param  string
     * @return void
     */
    public function deleteRecursive($path)
    {
        if (!$this->tryDelete($path)) {
            foreach ((array) $this->nlist($path) as $file) {
                if ($file !== '.' && $file !== '..') {
                    $this->deleteRecursive(strpos($file, '/') === FALSE ? "$path/$file" : $file);
                }
            }
            $this->rmdir($path);
        }
    }



}