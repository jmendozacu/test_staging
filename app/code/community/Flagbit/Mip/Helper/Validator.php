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
 * @copyright   2015 by Flagbit GmbH & Co. KG
 * @author      Flagbit Magento Team <magento@flagbit.de>
 */


/**
 * @package     Flagbit
 * @subpackage  Flagbit_Mip
 */
class Flagbit_Mip_Helper_Validator extends Mage_Core_Helper_Abstract {


    /**
     * Validates a file with simplexml or xmllint
     * returns TRUE is the file valid and a array with lines and error messages if not
     *
     * Example Output:
     * Array
     * (
     *     [377] => Array
     *         (
     *             [0] =>  parser error : error parsing attribute name
     *             [1] =>  parser error : attributes construct error
     *             [2] =>  parser error : Couldn't find end of Start Tag DATA_Abholdatum24.06.2013 line 377
     *             [3] =>  parser error : Opening and ending tag mismatch: NODE_Document line 362 and DATA_Abholdatum
     *         )
     *     [411] => Array
     *         (
     *             [0] =>  parser error : Extra content at the end of the document
     *         )
     * )
     * @param string $file
     * @param string $type
     *
     * @return array|bool
     * @throws Exception
     */
    public function validate($file, $type)
    {
        if(!file_exists($file) || !is_readable($file)){
            throw new Exception('file "'.$file.'" does not exists or is not readable.');
        }

        if(!filesize($file)){
            throw new Exception('file "'.$file.'" is empty.');
        }

        $functionName = '_'.$type.'Validate';
        if(method_exists($this, $functionName)){
            $returnValue = call_user_func(array($this, $functionName), $file);
        }else{
            throw new Exception('Validator '.$functionName.' does not exists.');
        }
        return $returnValue;
    }


    /**
     * validates a xml file with xmllint
     *
     * @param $file
     * @return array|bool
     * @throws Exception
     */
    protected function _xmllintValidate($file)
    {
        $filePathLength = strlen($file);
        $errorMessages = array();
        $cmd = 'xmllint --noout ' . escapeshellarg($file) . ' 2>&1';
        exec($cmd, $output, $retVal);

        if ($retVal == 0) {
            return true;
        }
        for ($i = 0; $i < count($output); $i += 3) {
            $line = $output[$i];
            if (substr($line, 0, $filePathLength) != $file) {
                throw new Exception('xmllint does not behave as expected: ' . $line);
            }
            list($lineNum, $msg) = explode(':', substr($line, $filePathLength + 1), 2);
            $errorMessages[$lineNum][] = $msg;
        }

        return $errorMessages;
    }

    /**
     * validates a xml file with simplexml
     *
     * @param $file
     * @return array|bool
     * @throws Exception
     */
    protected function _simplexmlValidate($file)
    {
        $errorMessages = array();
        libxml_use_internal_errors(true);
        libxml_clear_errors();
        $response = simplexml_load_file($file);
        if ($response === false) {
            $errors = libxml_get_errors();
            if(!empty($errors)) {
                foreach ($errors as $error) {
                    $errorMessages[$error->line][] = trim($error->message).' (Column: '.$error->column.')';
                }
            }
        }else{
            return true;
        }
        return $errorMessages;
    }


}