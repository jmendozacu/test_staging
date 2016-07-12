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
class Flagbit_Mip_Model_Server_Trigger_File extends Flagbit_Mip_Model_Server_Trigger_Abstract {

    /**
     * run Trigger
     * determines Request Type Input / Output
     */
    public function run()
    {
        switch($this->getDirection()){

            case 'input':
                $contents = array();
                $filelist = $this->_getFilelist();

                //get file content
                if(!empty($filelist['file'])){
                    foreach($filelist['file'] as $file){
                        $contents[] = $this->_getFileContent($file);
                    }
                }

                // get URL content
                if(!empty($filelist['url'])){
                    foreach($filelist['url'] as $file){
                        $contents[] = $this->_getUrlContent($file);
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
                    $fp = fopen(Mage::getBaseDir().$file, "w+");
                    fwrite($fp, $output);
                    fclose($fp);
                }
                break;
        }
    }

    /**
     * get filelist Array
     *
     * [url][]  => 'http://www.example.com/demo.txt'
     * [file][] => 'demo.txt'
     *
     * @return array
     */
    protected function _getFilelist()
    {
        $filesResultArray = array();
        $filesArray = explode(';', $this->_getDefinition()->getSettings('file'));
        foreach ($filesArray as $file){
            $file = $this->handleVariables($file);
            $type = (substr($file, 0, 7) == 'http://' || substr($file, 0, 8) == 'https://' ? 'url' : 'file');

            switch($type){

                case 'url':
                    $filesResultArray['url'][] = $file;
                    break;

                case 'file':
                    $file = Mage::getBaseDir().$file;
                    if(file_exists($file)){
                        $filesResultArray['file'][] = $file;
                    }else{
                        $files = glob($file);

                        switch($this->_getDefinition()->getSettings('file_sort')){

                            case 'name':
                                sort($files);
                                break;

                            default:
                                // sort the oldest files first
                                usort($files, function ($a, $b) {
                                    return filemtime($a) - filemtime($b);
                                });
                        }

                        if($this->_getDefinition()->getSettings('one_file_only')){
                            if(count($files)){
                                $filesResultArray['file'][] = current($files);
                            }
                        }else{
                            foreach($files as $globFile){
                                $filesResultArray['file'][] = $globFile;
                            }
                        }
                    }
                    break;
            }
        }
        return $filesResultArray;
    }

    /**
     * get Content from URL
     *
     * @param $url
     * @return string
     */
    protected function _getUrlContent($url)
    {
        $content = file_get_contents($url);
        Mage::helper('mip/log')->getWriter($this)->trace('get URL content: '.$url.' ('.round(strlen($content) / 1024 /1024, 2).' MB)');
        return $content;
    }

    /**
     * get File Content
     * deletes File after get Content when set in Config
     *
     * @param string $file
     * @return string
     */
    protected function _getFileContent($file)
    {
        if(file_exists($file)){

            // validate
            if($validator = $this->_getDefinition()->getSettings('validate')){
                $stopOnError = $this->_getDefinition()->getSettings('validate_error_handling') == 'stop';
                $this->_validateFile($file, $validator, $stopOnError);
            }

            // get file content
            $content = file_get_contents($file);
            Mage::helper('mip/log')->getWriter($this)->trace('get File content: '.$file.' ('.round(strlen($content) / 1024 /1024, 2).' MB)');

            // archive file
            if($this->_getDefinition()->getSettings('archive_file')){
                $this->_archiveFile($file, $this->_getDefinition()->getSettings('archive_file'));
            }

            // delete file
            if($this->_getDefinition()->getSettings('delete_file')){
                Mage::helper('mip/log')->getWriter($this)->trace('delete File: '.$file);
                unlink($file);
            }

        }else{
            Mage::helper('mip/log')->getWriter($this)->warn('cannot get File content ('.$file.')');
        }
        return $content;
    }

    /**
     * validate File
     *
     * @param $file
     * @param $validator
     * @param bool $stopOnError
     * @throws Exception
     */
    protected function _validateFile($file, $validator, $stopOnError = true)
    {
        Mage::helper('mip/log')->getWriter($this)->trace('validate File with '.$validator.': '.$file);
        $validatorResult = Mage::helper('mip/validator')->validate($file, $validator);

        if($validatorResult !== true){
            Mage::helper('mip/log')->getWriter($this)->error('Error the file "'.$file.'" is not valid!');
            foreach($validatorResult as $line => $errors){
                foreach ($errors as $error) {
                    Mage::helper('mip/log')->getWriter($this)->trace('validation Error on Line '.$line.': '.$error);
                }
            }
            if($stopOnError === true) {
                throw new Exception('Error the file "' . $file . '" is not valid!');
            }
        }
    }

    /**
     * copy a given file to a path and extend the
     * filename with an increment number if it already exists
     *
     * @param string $file
     * @param string $archive_path
     * @return boolean
     */
    protected function _archiveFile($file, $archive_path)
    {
        Mage::helper('mip/log')->getWriter($this)->trace('archive File: '.$file.' -> '.$archive_path);
        $result = false;
        $archive_path = rtrim(Mage::getBaseDir(), '/').DS.trim($archive_path, '/').DS;

        if(is_dir($archive_path) || mkdir($archive_path, 0755))
        {
            $fileInfo = pathinfo($file);
            $fileName = rtrim(substr($fileInfo['basename'], 0,  strlen($fileInfo['basename'])-strlen($fileInfo['extension'])), '.');
            $fileExtension = $fileInfo['extension'];

            preg_match_all('#'.preg_quote($fileName, '#').'\_([0-9]{1,})\.'.preg_quote($fileExtension, '#').'\|#',
                             join('|', (array) glob($archive_path.$fileName.'_*.'.$fileExtension)).'|',
                              $matches
                           );

            $suffix = empty($matches[1]) ? '' : '_'.(max($matches[1]) + 1);
            if(file_exists($archive_path.$fileName.$suffix.'.'.$fileExtension) && empty($suffix)){
                $suffix = '_1';
            }

            $result    = copy($file, $archive_path.$fileName.$suffix.'.'.$fileExtension);

        }else{
            Mage::logException(new Exception('archive_path \''.$archive_path.'\' doesnt exist nor cant get created'));
        }

        return $result;
    }
}